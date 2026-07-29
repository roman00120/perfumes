<?php
declare(strict_types=1);
final class CsvExportService{public function cell(mixed $v):string{$v=(string)$v;return preg_match('/^[=+@-]/',$v)?"'".$v:$v;}public function write(string $path,array $headers,iterable $rows):int{$h=fopen($path,'wb');fwrite($h,"\xEF\xBB\xBF");fputcsv($h,$headers);$count=0;foreach($rows as $row){fputcsv($h,array_map(fn($v)=>$this->cell($v),$row));$count++;}fclose($h);return $count;}}
