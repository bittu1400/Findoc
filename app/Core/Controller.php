<?php

namespace App\Core;

class Controller
{
    protected function view($view, $data = [])
    {
        // Extract data array to variables
        extract($data);
        
        // Build view path
        $viewPath = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View {$view} not found at {$viewPath}");
        }
        
        require_once $viewPath;
    }

    protected function redirect($path)
    {
        header("Location: /{$path}");
        exit;
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: {$referer}");
        exit;
    }

    protected function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $ruleArray = explode('|', $ruleSet);
            
            foreach ($ruleArray as $rule) {
                $ruleName = $rule;
                $ruleValue = null;

                // Handle rules with parameters (e.g., min:3)
                if (strpos($rule, ':') !== false) {
                    list($ruleName, $ruleValue) = explode(':', $rule);
                }

                $value = $data[$field] ?? null;

                switch ($ruleName) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = ucfirst($field) . " is required";
                        }
                        break;

                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = ucfirst($field) . " must be a valid email";
                        }
                        break;

                    case 'min':
                        if (!empty($value) && strlen($value) < $ruleValue) {
                            $errors[$field][] = ucfirst($field) . " must be at least {$ruleValue} characters";
                        }
                        break;

                    case 'max':
                        if (!empty($value) && strlen($value) > $ruleValue) {
                            $errors[$field][] = ucfirst($field) . " must not exceed {$ruleValue} characters";
                        }
                        break;

                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field][] = ucfirst($field) . " must be a number";
                        }
                        break;

                    case 'match':
                        if (!empty($value) && $value !== ($data[$ruleValue] ?? null)) {
                            $errors[$field][] = ucfirst($field) . " must match {$ruleValue}";
                        }
                        break;
                }
            }
        }

        return empty($errors) ? true : $errors;
    }

    protected function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    protected function old($field, $default = '')
    {
        return $_SESSION['old'][$field] ?? $default;
    }

    protected function setOldInput($data)
    {
        $_SESSION['old'] = $data;
    }

    protected function clearOldInput()
    {
        unset($_SESSION['old']);
    }

    protected function setFlash($key, $message)
    {
        $_SESSION['flash'][$key] = $message;
    }

    protected function getFlash($key)
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
}

?>