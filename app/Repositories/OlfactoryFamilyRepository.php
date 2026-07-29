<?php
declare(strict_types=1);
final class OlfactoryFamilyRepository extends TaxonomyRepository{public function __construct(PDO $db){parent::__construct($db,'family');}}
