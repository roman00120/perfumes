<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
if(PHP_SAPI!=='cli'){fwrite(STDERR,"Solo CLI.\n");exit(1);}
$db=Database::connection();
$items=[
 ['Bleu de Chanel Eau de Parfum','bleu-de-chanel-eau-de-parfum','Chanel','perfumes-para-hombre','hombre','eau_de_parfum','Aromático amaderado con cedro, sándalo y matices ambarados.','reference-blue.png'],
 ['Sauvage Eau de Toilette','sauvage-eau-de-toilette','Dior','perfumes-para-hombre','hombre','eau_de_toilette','Fragancia masculina fresca y especiada para explorar con asesoría personalizada.','reference-black.png'],
 ['Libre Eau de Parfum','libre-eau-de-parfum','Yves Saint Laurent','perfumes-para-mujer','mujer','eau_de_parfum','Una interpretación floral con lavanda, azahar y un fondo cálido.','reference-pink.png'],
 ['Good Girl Eau de Parfum','good-girl-eau-de-parfum','Carolina Herrera','perfumes-para-mujer','mujer','eau_de_parfum','Fragancia floral oriental con contraste entre notas luminosas y profundas.','reference-pink.png'],
 ['Coco Mademoiselle Eau de Parfum','coco-mademoiselle-eau-de-parfum','Chanel','perfumes-para-mujer','mujer','eau_de_parfum','Composición cítrica, floral y amaderada de carácter elegante.','reference-pink.png'],
 ['Acqua di Giò Eau de Parfum','acqua-di-gio-eau-de-parfum','Giorgio Armani','perfumes-para-hombre','hombre','eau_de_parfum','Aroma marino y amaderado inspirado en frescura y profundidad.','reference-green.png'],
 ['Y Eau de Parfum','y-eau-de-parfum','Yves Saint Laurent','perfumes-para-hombre','hombre','eau_de_parfum','Frescor aromático con facetas amaderadas para una presencia versátil.','reference-blue.png'],
 ['La Vie Est Belle Eau de Parfum','la-vie-est-belle-eau-de-parfum','Lancôme','perfumes-para-mujer','mujer','eau_de_parfum','Fragancia floral dulce y envolvente para descubrir en tienda.','reference-pink.png'],
 ['Eros Eau de Toilette','eros-eau-de-toilette','Versace','perfumes-para-hombre','hombre','eau_de_toilette','Aroma aromático amaderado de carácter intenso y contemporáneo.','reference-blue.png'],
 ['Light Blue Eau de Toilette','light-blue-eau-de-toilette','Dolce & Gabbana','perfumes-para-mujer','mujer','eau_de_toilette','Fragancia fresca y cítrica de inspiración mediterránea.','reference-green.png'],
];
$brand=$db->prepare('SELECT id FROM brands WHERE slug=? LIMIT 1');$cat=$db->prepare('SELECT id FROM categories WHERE slug=? LIMIT 1');$find=$db->prepare('SELECT id FROM perfumes WHERE slug=? LIMIT 1');$insert=$db->prepare("INSERT INTO perfumes (brand_id,category_id,name,slug,short_description,full_description,gender,concentration,main_image,status,availability_status,is_published,is_featured,show_price,meta_title,meta_description,robots) VALUES (?,?,?,?,?,?,?,?,?,'publicado','pendiente',1,0,0,?,?, 'index,follow')");$count=0;
foreach($items as [$name,$slug,$brandSlug,$categorySlug,$gender,$concentration,$description,$image]){$brand->execute([SlugService::make($brandSlug)]);$b=$brand->fetchColumn();$cat->execute([$categorySlug]);$c=$cat->fetchColumn();$find->execute([$slug]);if($find->fetchColumn()||!$b||!$c)continue;$insert->execute([(int)$b,(int)$c,$name,$slug,$description,$description,$gender,$concentration,'uploads/perfumes/'.$image,$name.' | Les Sens Perfumería',$description]);$count++;}
echo "Registros referenciales cargados: {$count}\n";
