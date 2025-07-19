<?php
/**
 * Input Validator Class
 * 
 * Handles input validation with proper error handling
 */

class Validator {
    private array $errors = [];
    private array $data = [];
    
    /**
     * Validate required field
     * 
     * @param string $field
     * @param mixed $value
     * @param string $message
     * @return self
     */
    public function required(string $field, $value, string $message = null): self {
        if (empty($value) || (is_string($value) && trim($value) === '')) {
            $this->errors[$field] = $message ?? ucfirst($field) . ' is required';
        } else {
            $this->data[$field] = $value;
        }
        return $this;
    }
    
    /**
     * Validate email format
     * 
     * @param string $field
     * @param string $value
     * @param string $message
     * @return self
     */
    public function email(string $field, string $value, string $message = null): self {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? 'Please enter a valid email address';
        } else if (!empty($value)) {
            $this->data[$field] = $value;
        }
        return $this;
    }
    
    /**
     * Validate minimum length
     * 
     * @param string $field
     * @param string $value
     * @param int $min
     * @param string $message
     * @return self
     */
    public function minLength(string $field, string $value, int $min, string $message = null): self {
        if (!empty($value) && strlen($value) < $min) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must be at least {$min} characters long";
        } else if (!empty($value)) {
            $this->data[$field] = $value;
        }
        return $this;
    }
    
    /**
     * Validate maximum length
     * 
     * @param string $field
     * @param string $value
     * @param int $max
     * @param string $message
     * @return self
     */
    public function maxLength(string $field, string $value, int $max, string $message = null): self {
        if (!empty($value) && strlen($value) > $max) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must not exceed {$max} characters";
        } else if (!empty($value)) {
            $this->data[$field] = $value;
        }
        return $this;
    }
    
    /**
     * Validate date format
     * 
     * @param string $field
     * @param string $value
     * @param string $format
     * @param string $message
     * @return self
     */
    public function date(string $field, string $value, string $format = 'Y-m-d', string $message = null): self {
        if (!empty($value)) {
            $date = DateTime::createFromFormat($format, $value);
            if (!$date || $date->format($format) !== $value) {
                $this->errors[$field] = $message ?? 'Please enter a valid date';
            } else {
                $this->data[$field] = $value;
            }
        }
        return $this;
    }
    
    /**
     * Validate numeric value
     * 
     * @param string $field
     * @param mixed $value
     * @param string $message
     * @return self
     */
    public function numeric(string $field, $value, string $message = null): self {
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field] = $message ?? ucfirst($field) . ' must be a valid number';
        } else if (!empty($value)) {
            $this->data[$field] = $value;
        }
        return $this;
    }
    
    /**
     * Validate minimum value
     * 
     * @param string $field
     * @param numeric $value
     * @param numeric $min
     * @param string $message
     * @return self
     */
    public function min(string $field, $value, $min, string $message = null): self {
        if (!empty($value) && is_numeric($value) && $value < $min) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must be at least {$min}";
        } else if (!empty($value)) {
            $this->data[$field] = $value;
        }
        return $this;
    }
    
    /**
     * Custom validation rule
     * 
     * @param string $field
     * @param mixed $value
     * @param callable $callback
     * @param string $message
     * @return self
     */
    public function custom(string $field, $value, callable $callback, string $message): self {
        if (!empty($value) && !$callback($value)) {
            $this->errors[$field] = $message;
        } else if (!empty($value)) {
            $this->data[$field] = $value;
        }
        return $this;
    }
    
    /**
     * Check if validation passed
     * 
     * @return bool
     */
    public function passes(): bool {
        return empty($this->errors);
    }
    
    /**
     * Check if validation failed
     * 
     * @return bool
     */
    public function fails(): bool {
        return !empty($this->errors);
    }
    
    /**
     * Get validation errors
     * 
     * @return array
     */
    public function getErrors(): array {
        return $this->errors;
    }
    
    /**
     * Get first error message
     * 
     * @return string|null
     */
    public function getFirstError(): ?string {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
    
    /**
     * Get validated data
     * 
     * @return array
     */
    public function getData(): array {
        return $this->data;
    }
    
    /**
     * Get specific validated field
     * 
     * @param string $field
     * @return mixed
     */
    public function get(string $field) {
        return $this->data[$field] ?? null;
    }
}
