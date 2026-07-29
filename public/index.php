<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';

try { $db = Database::connection(); }
catch (Throwable $e) { $db = null; error_log('Public bootstrap database unavailable: '.$e->getMessage()); }

if ($db && !in_array(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), ['/health','/health/readiness'], true)) {
    try { $redirect = (new RedirectService($db))->find((string)(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/')); if ($redirect) { (new RedirectService($db))->hit((int)$redirect['id']); header('Location: '.(string)$redirect['target_url'], true, (int)$redirect['status_code']); exit; } } catch (Throwable $e) { error_log('Redirect lookup failed: '.$e->getMessage()); }
}

$router = new Router();
$homeController = new HomeController($db);
$router->get('/', fn() => $homeController->index());
$sitemapController = new SitemapController($db);
$router->get('/sitemap.xml', fn() => $sitemapController->index());
$router->get('/sitemap-index.xml', fn() => $sitemapController->index());
foreach (['pages','perfumes','brands','categories','promotions'] as $sitemapType) $router->get('/sitemaps/'.$sitemapType.'.xml', fn() => $sitemapController->xml($sitemapType));
$router->get('/robots.txt', fn() => (new RobotsController())->show());
$healthController = new HealthController($db);
$router->get('/health', fn() => $healthController->show());
$router->get('/health/readiness', fn() => $healthController->show(true));
if ($db) { $analyticsController = new AnalyticsController($db); $router->post('/analytics/event', fn() => $analyticsController->event()); }

if ($db) {
    $catalog = new CatalogController($db);
    $perfumes = new PerfumeController($db);
    $brands = new BrandController($db);
    $categories = new CategoryController($db);
    $router->get('/catalogo', fn() => $catalog->index());
    $router->get('/catalogo/pagina/{numero}', fn($numero) => $catalog->index((int)$numero));
    $router->get('/buscar', fn() => $catalog->index());
    $router->get('/perfume/{slug}', fn($slug) => $perfumes->show($slug));
    $router->get('/marcas', fn() => $brands->index());
    $router->get('/marca/{slug}', fn($slug) => $brands->show($slug));
    $router->get('/categorias', fn() => $categories->index());
    $router->get('/categoria/{slug}', fn($slug) => $categories->show($slug));
} else {
    $unavailable = fn() => view('errors/500', ['message' => 'El catálogo no está disponible temporalmente.']);
    foreach (['/catalogo','/buscar','/marcas','/marca/{slug}','/categorias','/categoria/{slug}','/perfume/{slug}'] as $path) $router->get($path, $unavailable);
}

$publicContent=$db?new PublicContentController($db):null;
$router->get('/promociones', fn()=> $publicContent?$publicContent->promotions():view('website/pages/placeholder',['title'=>'Promociones','message'=>'Consulta nuestras novedades directamente con el equipo.']));
$router->get('/promociones/{slug}', fn($slug)=>$publicContent?$publicContent->promotion($slug):view('errors/500'));
$router->get('/pagina/{slug}', fn($slug)=>$publicContent?$publicContent->page($slug):view('errors/500'));
$router->get('/preguntas-frecuentes', fn()=> $publicContent?$publicContent->faqs():view('errors/500'));
$router->get('/nosotros', fn() => view('website/pages/placeholder', ['title'=>'Nosotros','message'=>'Una atención cercana para ayudarte a explorar fragancias, cosmética y cuidado personal.']));
$router->get('/contacto', fn() => view('website/pages/contact'));
if($db){$contactController=new PublicContactController($db);$router->post('/contacto',fn()=> $contactController->submit());}

if ($db) {
    $auth = new AdminAuthService($db); $middleware = new AdminAuthMiddleware($auth);
    $authController = new AdminAuthController($auth); $dashboard = new AdminDashboardController($db); $placeholder = new AdminPlaceholderController();
    $router->get('/admin', fn() => redirect('admin/dashboard')); $router->get('/admin/login', fn() => $authController->loginForm());
    $router->post('/admin/login', fn() => $authController->login()); $router->post('/admin/logout', fn() => $authController->logout()); $router->get('/admin/logout', fn() => redirect('admin/login'));
    $protected = fn(callable $handler) => function (...$args) use ($handler, $middleware) { if (!$middleware->enforce()) return ''; return $handler(...$args); };
    $router->get('/admin/dashboard', $protected(fn() => $dashboard->index()));
    $adminPerfumes = new AdminPerfumeController($db);
    $router->get('/admin/perfumes', $protected(fn() => $adminPerfumes->index()));
    $router->get('/admin/perfumes/crear', $protected(fn() => $adminPerfumes->create()));
    $router->post('/admin/perfumes', $protected(fn() => $adminPerfumes->store()));
    $router->get('/admin/perfumes/{id}/editar', $protected(fn($id) => $adminPerfumes->edit($id)));
    $router->get('/admin/perfumes/{id}/vista-previa', $protected(fn($id) => $adminPerfumes->preview($id)));
    $router->get('/admin/perfumes/{id}', $protected(fn($id) => $adminPerfumes->show($id)));
    $router->post('/admin/perfumes/{id}/actualizar', $protected(fn($id) => $adminPerfumes->update($id)));
    foreach(['publicar','despublicar','archivar','restaurar','eliminar','eliminar-definitivamente'] as $action) $router->post('/admin/perfumes/{id}/'.$action, $protected(fn($id) => $adminPerfumes->action($id,$action)));
    $router->post('/admin/perfumes/{id}/duplicar', $protected(fn($id) => $adminPerfumes->duplicate($id)));
    $router->post('/admin/perfumes/{id}/imagenes', $protected(fn($id) => $adminPerfumes->images($id)));
    foreach(['principal','orden','eliminar'] as $imageAction) $router->post('/admin/perfumes/{id}/imagenes/{imageId}/'.$imageAction, $protected(fn($id,$imageId) => $adminPerfumes->imageAction($id,$imageId,$imageAction)));
    $router->post('/admin/perfumes/{id}/presentaciones', $protected(fn($id) => $adminPerfumes->presentationAction($id)));
    $router->post('/admin/perfumes/{id}/presentaciones/{presentationId}/actualizar', $protected(fn($id,$presentationId) => $adminPerfumes->presentationAction($id,$presentationId)));
    $router->post('/admin/perfumes/{id}/presentaciones/{presentationId}/eliminar', $protected(fn($id,$presentationId) => $adminPerfumes->presentationDelete($id,$presentationId)));
    $router->post('/admin/perfumes/{id}/notas', $protected(fn($id) => $adminPerfumes->noteAction($id)));
    $router->post('/admin/perfumes/{id}/notas/{noteId}/actualizar', $protected(fn($id,$noteId) => $adminPerfumes->noteAction($id)));
    $adminBrands=new AdminBrandController($db);$adminCategories=new AdminCategoryController($db);$adminFamilies=new AdminOlfactoryFamilyController($db);$adminNotes=new AdminNoteController($db);$adminTags=new AdminTagController($db);
    $taxonomyApi=new AdminTaxonomyApiController($db);foreach(['marcas','categorias','familias-olfativas','notas','etiquetas'] as $apiSlug)$router->get('/admin/api/'.$apiSlug.'/buscar',$protected(fn()=>$taxonomyApi->search($apiSlug)));
    $taxonomyRoutes=[['marcas',$adminBrands],['categorias',$adminCategories],['familias-olfativas',$adminFamilies],['notas',$adminNotes],['etiquetas',$adminTags]];
    foreach($taxonomyRoutes as [$slug,$controller]){$router->get('/admin/'.$slug,$protected(fn()=>$controller->index()));$router->get('/admin/'.$slug.'/crear',$protected(fn()=>$controller->create()));$router->post('/admin/'.$slug,$protected(fn()=>$controller->store()));$router->get('/admin/'.$slug.'/{id}/editar',$protected(fn($id) =>$controller->edit($id)));$router->get('/admin/'.$slug.'/{id}',$protected(fn($id) =>$controller->show($id)));$router->post('/admin/'.$slug.'/{id}/actualizar',$protected(fn($id) =>$controller->update($id)));foreach(['activar','desactivar','eliminar','restaurar','eliminar-definitivamente','duplicar','fusionar'] as $action)$router->post('/admin/'.$slug.'/{id}/'.$action,$protected(fn($id) =>$controller->action($id,$action)));}
    $router->post('/admin/marcas/{id}/aliases',$protected(fn($id)=>$adminBrands->alias($id)));$router->post('/admin/marcas/{id}/aliases/{aliasId}/eliminar',$protected(fn($id,$aliasId)=>$adminBrands->aliasDelete($id,$aliasId)));
    $router->post('/admin/perfumes/{id}/notas/{noteId}/eliminar', $protected(fn($id,$noteId) => $adminPerfumes->noteDelete($id)));
    $sections = ['perfumes'=>'Perfumes','marcas'=>'Marcas','categorias'=>'Categorías','promociones'=>'Promociones','banners'=>'Banners','paginas'=>'Páginas','faqs'=>'Preguntas frecuentes','testimonios'=>'Testimonios','mensajes'=>'Mensajes','horarios'=>'Horarios','redes'=>'Redes sociales','media'=>'Biblioteca multimedia','configuracion'=>'Configuración'];
    $router->post('/admin/marcas/{id}/logo',$protected(fn($id)=>$adminBrands->media($id)));$router->post('/admin/marcas/{id}/logo/eliminar',$protected(fn($id)=>$adminBrands->mediaDelete($id)));
    $router->post('/admin/categorias/{id}/imagen',$protected(fn($id)=>$adminCategories->media($id)));$router->post('/admin/categorias/{id}/imagen/eliminar',$protected(fn($id)=>$adminCategories->mediaDelete($id)));
    $router->post('/admin/familias-olfativas/{id}/imagen',$protected(fn($id)=>$adminFamilies->media($id)));$router->post('/admin/familias-olfativas/{id}/imagen/eliminar',$protected(fn($id)=>$adminFamilies->mediaDelete($id)));
    $router->post('/admin/notas/{id}/imagen',$protected(fn($id)=>$adminNotes->media($id)));$router->post('/admin/notas/{id}/imagen/eliminar',$protected(fn($id)=>$adminNotes->mediaDelete($id)));
    $contentControllers=[['promociones',new AdminPromotionController($db)],['banners',new AdminBannerController($db)],['paginas',new AdminPageController($db)],['faqs',new AdminFaqController($db)],['testimonios',new AdminTestimonialController($db)],['mensajes',new AdminContactMessageController($db)],['redes-sociales',new AdminSocialLinkController($db)],['multimedia',new AdminMediaController($db)]];
    foreach($contentControllers as [$slug,$controller]){$router->get('/admin/'.$slug,$protected(fn()=>$controller->index()));$router->get('/admin/'.$slug.'/crear',$protected(fn()=>$controller->create()));$router->post('/admin/'.$slug,$protected(fn()=>$controller->store()));$router->get('/admin/'.$slug.'/{id}',$protected(fn($id)=>$controller->show($id)));$router->get('/admin/'.$slug.'/{id}/editar',$protected(fn($id)=>$controller->edit($id)));$router->post('/admin/'.$slug.'/{id}/actualizar',$protected(fn($id)=>$controller->update($id)));foreach(['activar','desactivar','publicar','despublicar','verificar','quitar-verificacion','marcar-leido','marcar-no-leido','marcar-atendido','marcar-pendiente','archivar','eliminar','restaurar','eliminar-definitivamente']as$action)$router->post('/admin/'.$slug.'/{id}/'.$action,$protected(fn($id)=>$controller->action($id,$action)));}
    foreach ($sections as $slug => $title) $router->get('/admin/'.$slug, $protected(fn() => $placeholder->show($slug, $title)));
    $imports=new AdminImportController($db);$router->get('/admin/importaciones',$protected(fn()=>$imports->index()));$router->get('/admin/importaciones/nueva',$protected(fn()=>$imports->create()));$router->post('/admin/importaciones/subir',$protected(fn()=>$imports->upload()));$router->get('/admin/importaciones/plantilla/{tipo}',$protected(fn($tipo)=>$imports->template($tipo)));$router->get('/admin/importaciones/{id}',$protected(fn($id)=>$imports->show($id)));$router->post('/admin/importaciones/{id}/confirmar',$protected(fn($id)=>$imports->confirm($id)));
    $exports=new AdminExportController($db);$router->get('/admin/exportaciones',$protected(fn()=>$exports->index()));$router->post('/admin/exportaciones/generar',$protected(fn()=>$exports->generate()));$router->get('/admin/exportaciones/{id}',$protected(fn($id)=>$exports->show($id)));$router->get('/admin/exportaciones/{id}/descargar',$protected(fn($id)=>$exports->download($id)));
    $router->get('/admin/seo', $protected(fn() => (new AdminSeoController($db))->index()));
    $router->post('/admin/seo/auditar', $protected(fn() => (new AdminSeoController($db))->audit()));
    $router->get('/admin/sistema/lanzamiento', $protected(fn() => (new AdminLaunchController($db))->index()));
} else {
    $router->get('/admin', fn() => view('errors/500', ['message'=>'El área administrativa no está disponible temporalmente.']));
    $router->get('/admin/login', fn() => view('errors/500', ['message'=>'El acceso administrativo no está disponible temporalmente.']));
}

try { echo $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/'); }
catch (Throwable $e) { error_log((string)$e); http_response_code(500); echo view('errors/500', ['message'=>AppConfig::$data['debug'] ? $e->getMessage() : 'Ocurrió un error inesperado.']); }
