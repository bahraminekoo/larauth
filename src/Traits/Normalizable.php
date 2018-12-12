<?php

namespace Bahraminekoo\Larauth\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;

trait Normalizable {

    public function normalize() {
        $this->kind;
        $entities = [];

        $entityKey = Str::plural(Str::camel($this->kind));
        $mutatedAttributes = $this->getMutatedAttributes();

        $entities[$entityKey] = [];
        $selfData = $this->toArray(true);
        $relations = $this->getArrayableRelations();
        foreach ($relations as $key => $relation) {
            // custom relationship transformer, transform before normalize.
            if (method_exists($this, $key.'Transformer')) {
                $transformed = $this->{"{$key}Transformer"}();
                $key = $transformed['key'];
                $relation = $transformed['value'];
            }
            if($relation instanceof Pivot) {
                $selfData[$key] = $relation->toArray();
            } else if ($relation instanceof Collection) {
                $selfData[$key] = [];
                foreach ($relation->getIterator() as $item) {
                    if ($item->shouldBeNormalizable()) {
                        $selfData[$key][] = $item->getKey();
                        $entities = array_replace_recursive($entities, $item->normalize()['entities']);
                    } else {
                        $selfData[$key][] = $item->toArray();
                    }
                }
            } else if(isset($relation)) {
                if ($relation->shouldBeNormalizable()) {
                    if (in_array($key, $mutatedAttributes)) {
                        $selfData[$key] = $this->mutateAttributeForArray($key, $relation);
                    } else {
                        $selfData[$key] = $relation->getKey();
                    }
                    $entities = array_replace_recursive($entities, $relation->normalize()['entities']);
                } else {
                    if (in_array($key, $mutatedAttributes)) {
                        $selfData[$key] = $this->mutateAttributeForArray($key, $relation);
                    } else {
                        $selfData[$key] = $relation->toArray();
                    }
                }
            }
        }
        $entities[$entityKey][$this->getKey()] = $selfData;

        $output = [];
        $output[Str::camel($this->kind)] = $this->getKey();
        $output['entities'] = $entities;

        return $output;
    }

    public function shouldBeNormalizable() {
        return true;
    }
}
