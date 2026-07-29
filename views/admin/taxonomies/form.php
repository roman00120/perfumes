<?php
$p=$taxonomy??[];$edit=($mode??'')==='edit';$action=$edit?url('admin/'.$meta['slug'].'/'.$p['id'].'/actualizar'):url('admin/'.$meta['slug']);ob_start();
?>
<form class="perfume-form" method="post" action="<?=$action?>"><?=csrf_field()?><?php if($errors):?><div class="admin-alert danger" role="alert"><?php foreach($errors as $error):?><div><?=e($error)?></div><?php endforeach;?></div><?php endif;?>
<section class="panel form-section"><h2><?=$meta['title']?></h2><div class="form-grid">
<label>Nombre *<input required maxlength="160" name="name" value="<?=e($p['name']??'')?>"></label>
<label>Slug<input name="slug" value="<?=e($p['slug']??'')?>"></label>
<?php if($type==='category'):?><label>Categoría padre<select name="parent_id"><option value="">Sin padre</option><?php foreach($options as $o):?><option value="<?=$o['id']?>" <?=((string)($p['parent_id']??'')===(string)$o['id'])?'selected':''?>><?=e($o['name'])?></option><?php endforeach;?></select></label><?php endif;?>
<?php if($type==='brand'):?><label>País de origen<input name="country" value="<?=e($p['country']??'')?>"></label><label>Sitio web oficial<input type="url" name="website_url" value="<?=e($p['website_url']??'')?>"></label><?php endif;?>
<?php if($type==='note'):?><label>Grupo olfativo<input name="note_group" value="<?=e($p['note_group']??'')?>"></label><?php endif;?>
</div><label>Descripción<textarea name="description" rows="6"><?=e($p['description']??'')?></textarea></label>
<div class="form-actions"><label><input type="checkbox" name="is_active" value="1" <?=(!isset($p['is_active'])||$p['is_active'])?'checked':''?>> Activo</label><?php if(in_array($type,['brand','category'],true)):?><label><input type="checkbox" name="is_featured" value="1" <?=!empty($p['is_featured'])?'checked':''?>> Destacado</label><?php endif;?><label>Orden<input type="number" name="sort_order" min="0" value="<?=e($p['sort_order']??0)?>"></label></div></section>
<section class="panel form-section"><h2>Imagen o recurso</h2><p class="muted">Usa únicamente rutas locales autorizadas; no se descargan imágenes externas.</p><?php if($type==='brand'):?><label>Logo<input name="logo" value="<?=e($p['logo']??'')?>"></label><?php elseif(in_array($type,['category','note'],true)):?><label>Imagen<input name="image" value="<?=e($p['image']??'')?>"></label><?php endif;?></section>
<div class="form-actions"><button class="admin-button primary">Guardar</button><a class="admin-button secondary" href="<?=url('admin/'.$meta['slug'])?>">Cancelar</a></div></form>
<?php $content=ob_get_clean();$section=$meta['slug'];require dirname(__DIR__).'/layouts/main.php';?>
