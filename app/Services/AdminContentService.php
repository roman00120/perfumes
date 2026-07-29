<?php
declare(strict_types=1);
class AdminContentService{
 public function __construct(private PDO$db,private string$type){$this->repo=new ContentRepository($db,$type);$this->audit=new AuditService($db);}
 private ContentRepository$repo;private AuditService$audit;
 public function save(array$d,?int$id=null):int{$allowed=['title','slug','short_description','full_description','image','mobile_image','promotion_type','status','discount_text','starts_at','ends_at','is_active','is_featured','button_text','button_url','whatsapp_message','sort_order','meta_title','meta_description','name','subtitle','description','desktop_image','position','text_alignment','overlay_opacity','excerpt','content','featured_image','is_published','published_at','question','answer','category','author_name','author_avatar','quote','rating','is_verified','consent_obtained','source','platform','label','url','icon','phone','email','subject','message','internal_note','is_read'];$d=array_intersect_key($d,array_flip($allowed));if($this->type==='promotion'&&empty($d['status']))$d['status']='borrador';if($this->type==='page'&&empty($d['status']))$d['status']='borrador';$saved=$this->repo->save($d,$id);$this->audit->record($id?'content_updated':'content_created',$this->type,$saved);return$saved;}
 public function action(int$id,string$action):void{$this->repo->state($id,$action);$this->audit->record('content_'.$action,$this->type,$id);}
}
