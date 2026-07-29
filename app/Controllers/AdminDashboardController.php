<?php
declare(strict_types=1);
final class AdminDashboardController {
    public function __construct(private PDO $db) {}
    public function index(): string {
        admin_security_headers();
        $stats=[];$alerts=[];$dbError=null;
        try {
            $queries=['perfumes_total'=>"SELECT COUNT(*) FROM perfumes WHERE deleted_at IS NULL",'perfumes_publicados'=>"SELECT COUNT(*) FROM perfumes WHERE deleted_at IS NULL AND status='publicado' AND is_published=1",'perfumes_borrador'=>"SELECT COUNT(*) FROM perfumes WHERE deleted_at IS NULL AND status='borrador'",'perfumes_pendientes'=>"SELECT COUNT(*) FROM perfumes WHERE deleted_at IS NULL AND availability_status='pendiente'",'marcas_activas'=>"SELECT COUNT(*) FROM brands WHERE deleted_at IS NULL AND is_active=1",'categorias_activas'=>"SELECT COUNT(*) FROM categories WHERE deleted_at IS NULL AND is_active=1",'promociones_activas'=>"SELECT COUNT(*) FROM promotions WHERE deleted_at IS NULL AND is_active=1 AND (starts_at IS NULL OR starts_at<=NOW()) AND (ends_at IS NULL OR ends_at>=NOW())",'mensajes_nuevos'=>"SELECT COUNT(*) FROM contact_messages WHERE deleted_at IS NULL AND status='nuevo'"];foreach($queries as $k=>$q)$stats[$k]=(int)$this->db->query($q)->fetchColumn();
            if($stats['perfumes_pendientes']>0)$alerts[]=['type'=>'warning','text'=>$stats['perfumes_pendientes'].' perfumes tienen disponibilidad pendiente.'];
            foreach([['sin_imagen',"SELECT COUNT(*) FROM perfumes WHERE deleted_at IS NULL AND (main_image='' OR main_image IS NULL)",'Perfumes sin imagen'],['sin_categoria',"SELECT COUNT(*) FROM perfumes WHERE deleted_at IS NULL AND category_id IS NULL",'Perfumes sin categoría'],['sin_marca',"SELECT COUNT(*) FROM perfumes WHERE deleted_at IS NULL AND brand_id IS NULL",'Perfumes sin marca']] as [$key,$q,$label]){$n=(int)$this->db->query($q)->fetchColumn();if($n>0)$alerts[]=['type'=>'warning','text'=>$n.' '.$label.'.'];}
        }catch(Throwable $e){$dbError='No fue posible consultar los indicadores en este momento.';error_log('Dashboard database error: '.$e->getMessage());}
        if(Env::get('ADMIN_PASSWORD_HASH','')==='')$alerts[]=['type'=>'danger','text'=>'La contraseña administrativa aún no está configurada.'];
        if(Env::get('SECURITY_PEPPER','')==='')$alerts[]=['type'=>'danger','text'=>'Falta configurar SECURITY_PEPPER.'];
        if(Env::get('APP_ENV')!=='local'&&Env::bool('APP_DEBUG',false))$alerts[]=['type'=>'danger','text'=>'APP_DEBUG está activo fuera del entorno local.'];
        if(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off'&&!Env::bool('SESSION_COOKIE_SECURE',true))$alerts[]=['type'=>'danger','text'=>'SESSION_COOKIE_SECURE debe estar activo bajo HTTPS.'];
        return view('admin/pages/dashboard',['stats'=>$stats,'alerts'=>$alerts,'dbError'=>$dbError,'title'=>'Dashboard']);
    }
}
