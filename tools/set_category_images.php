<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';

$images = [
    'cosm-etica' => 'assets/images/categories/cosmetica.png',
    'cuidado-personal' => 'assets/images/categories/cuidado-personal.png',
    'perfumes-arabes' => 'assets/images/categories/perfumes-arabes.png',
    'perfumes-de-dise-nador' => 'assets/images/categories/perfumes-disenador.png',
    'perfumes-infantiles' => 'assets/images/categories/perfumes-infantiles.png',
    'perfumes-nicho' => 'assets/images/categories/perfumes-nicho.png',
];

$db = Database::connection();
$update = $db->prepare('UPDATE categories SET image=? WHERE slug=?');
$count = 0;
foreach ($images as $slug => $path) {
    $update->execute([$path, $slug]);
    $count += $update->rowCount();
}
echo "Imágenes de categorías actualizadas: {$count}\n";
