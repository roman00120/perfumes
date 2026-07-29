<?php
declare(strict_types=1);
final class AdminPlaceholderController {
    public function show(string $section,string $title): string { admin_security_headers();return view('admin/pages/placeholder',['section'=>$section,'title'=>$title]); }
}
