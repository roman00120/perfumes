<?php
ob_start();
$json=['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>array_map(fn($x)=>['@type'=>'Question','name'=>$x['question'],'acceptedAnswer'=>['@type'=>'Answer','text'=>$x['answer']]],$items)];
?>
<section class="page-hero"><div class="site-container"><p class="eyebrow">AYUDA</p><h1>Preguntas frecuentes</h1></div></section><section class="page-section"><div class="site-container"><?php foreach($items as $item):?><details class="faq-item"><summary><?=e($item['question'])?></summary><div><?=e($item['answer'])?></div></details><?php endforeach;?></div></section>
<?php $headExtra='<script type="application/ld+json">'.json_encode($json,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>';$content=ob_get_clean();require dirname(__DIR__).'/layouts/main.php';?>
