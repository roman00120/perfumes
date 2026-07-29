<?php
declare(strict_types=1);
final class AdminAuthController {
    public function __construct(private AdminAuthService $auth) {}
    public function loginForm(): string { admin_security_headers();return view('admin/pages/login',['error'=>flash('error'),'success'=>flash('success')]); }
    public function login(): string {
        admin_security_headers();
        if(!verify_csrf()){http_response_code(419);$this->audit('admin_csrf_failed');return view('admin/pages/login',['error'=>'La sesión del formulario expiró. Inténtalo nuevamente.']);}
        $result=$this->auth->attempt($_POST['usuario']??null,$_POST['password']??null);
        if($result['ok'])redirect('admin/dashboard');
        $message=$result['reason']==='blocked'?'Se han detectado varios intentos fallidos. Inténtalo nuevamente más tarde.':'Las credenciales ingresadas no son correctas.';
        flash('error',$message);return view('admin/pages/login',['error'=>$message]);
    }
    public function logout(): never {
        admin_security_headers();if(!verify_csrf()){http_response_code(419);echo view('errors/500',['message'=>'La sesión del formulario expiró.']);exit;}$this->auth->logout();flash('success','Sesión cerrada correctamente.');redirect('admin/login');
    }
    private function audit(string $action): void { try{(new AuditService(Database::connection()))->record($action,'admin_session');}catch(Throwable $e){error_log('Audit error: '.$e->getMessage());} }
}
