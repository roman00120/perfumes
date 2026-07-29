<?php
declare(strict_types=1);
final class TagRepository extends TaxonomyRepository{public function __construct(PDO $db){parent::__construct($db,'tag');}}
