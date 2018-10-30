<?php

namespace DeadMerc\Test\Traits;

trait Validation
{
    public function validate()
    {
        foreach ($this->validation as $field => $rule) {
            if (!is_callable($rule)) {
                throw new \Exception('Wrong validation rule:' . $rule);
            }
            if ($rule($this->$field) == false) {
                throw new \Exception('Please check field:' . $field . '-' . $this->$field . ', must be:' . $rule);
            }
        }
    }
}