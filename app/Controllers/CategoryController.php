<?php
declare(strict_types=1);
final class CategoryController {
    public function __construct(private PDO $db) {}
    public function index(): string {return view('website/categories/index',['categories'=>(new PerfumeRepository($this->db))->publicCategories(),'title'=>'Categorías','pageTitle'=>'Categorías de fragancias | Les Sens']);}
    public function show(string $slug): string {$repo=new PerfumeRepository($this->db);$category=$repo->publicCategory(strtolower($slug));if(!$category){http_response_code(404);return view('errors/404');}$result=$repo->paginate(['category'=>$category['slug']],max(1,(int)($_GET['page']??1)),Env::int('CATALOG_PER_PAGE',12));return view('website/categories/show',['category'=>$category,'result'=>$result,'title'=>$category['name'],'pageTitle'=>$category['name'].' | Les Sens Perfumería']);}
}
