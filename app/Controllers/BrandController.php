<?php
declare(strict_types=1);
final class BrandController {
    public function __construct(private PDO $db) {}
    public function index(): string {return view('website/brands/index',['brands'=>(new PerfumeRepository($this->db))->publicBrands(),'title'=>'Marcas','pageTitle'=>'Marcas de perfumería | Les Sens']);}
    public function show(string $slug): string {$repo=new PerfumeRepository($this->db);$brand=$repo->publicBrand(strtolower($slug));if(!$brand){http_response_code(404);return view('errors/404');}$result=$repo->paginate(['brand'=>$brand['slug']],max(1,(int)($_GET['page']??1)),Env::int('CATALOG_PER_PAGE',12));return view('website/brands/show',['brand'=>$brand,'result'=>$result,'title'=>$brand['name'],'pageTitle'=>$brand['name'].' | Les Sens Perfumería']);}
}
