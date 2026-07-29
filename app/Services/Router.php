<?php
declare(strict_types=1);
final class Router {
    private array $routes = [];
    public function get(string $path, callable $handler): void { $this->add('GET',$path,$handler); }
    public function post(string $path, callable $handler): void { $this->add('POST',$path,$handler); }
    private function add(string $method,string $path,callable $handler): void { $this->routes[] = compact('method','path','handler'); }
    public function dispatch(string $method,string $uri): mixed {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/'; $path = '/'.trim($path,'/'); if ($path !== '/') $path = rtrim($path,'/');
        foreach ($this->routes as $route) {
            $pattern = preg_replace('#\{[^/]+\}#','([^/]+)',$route['path']);
            if ($route['method'] !== $method || !preg_match('#^'.rtrim($pattern,'/').'/?$#',$path,$m)) continue;
            array_shift($m); return ($route['handler'])(...array_map('urldecode',$m));
        }
        http_response_code(404); return view('errors/404');
    }
}

