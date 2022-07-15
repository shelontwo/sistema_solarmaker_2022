<?php

namespace App\Services\S3;

use Aws\S3\S3Client;
Use Image;

class S3Service
{
    public function enviaS3($arquivo, $pasta)
    {
        $key = $pasta.'/'.md5(date('c') . uniqid()) . '.' . $arquivo->getClientOriginalName();

        return $this->S3()->putObject([
            'Bucket' => env('AWS_BUCKET',  'odoigo-db-backup'),
            'Key' => $key,
            'ContentType' => $arquivo->getClientMimeType(),
            'Body' => file_get_contents(realpath($arquivo)),
            'ACL' => 'public-read'
        ]);
    }

    public function enviaS3multi($arquivo, $pasta, $index = null)
    {
        $key = $pasta.'/'.md5(date('c') . uniqid()) . '.' . $arquivo['name'];

        if ($index) {
            $key = $pasta.'/'.md5(date('c') . uniqid()) . '.' . $arquivo['name'][$index];
        }
        
        return $this->S3()->putObject([
            'Bucket' => env('AWS_BUCKET',  'odoigo-db-backup'),
            'Key' => $key,
            'ContentType' => $index ? $arquivo['type'][$index] :  $arquivo['type'],
            'Body' => $index ? file_get_contents(realpath($arquivo['tmp_name'][$index])) : file_get_contents(realpath($arquivo['tmp_name'])),
            'ACL' => 'public-read'
        ]);
    }

    public function enviaS3miniatura($arquivo, $pasta)
    {
        $novoArquivo = '';
        $newImage = Image::make($arquivo['tmp_name']);
        $newImage->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $novoArquivo = $newImage->orientate()->stream()->__toString();

        $key = $pasta.'/min_'.md5(date('c') . uniqid()) . '.' . $arquivo['name'];
        
        return $this->S3()->putObject([
            'Bucket' => env('AWS_BUCKET',  'odoigo-db-backup'),
            'Key' => $key,
            'ContentType' => $arquivo['type'],
            'Body' => $novoArquivo,
            'ACL' => 'public-read'
        ]);
    }

    public function deletaS3($url)
    {
        $arquivo = explode('amazonaws.com/', $url);

        return $this->S3()->deleteObject([
            'Bucket' => env('AWS_BUCKET',  'odoigo-db-backup'),
            'Key' => $arquivo[1],
        ]);
    }

    protected function S3()
    {
        return new S3Client([
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID', 'AKIAWX5ZUZGHBXKE3DMZ'),
                'secret' => env('AWS_SECRET_ACCESS_KEY', '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'),
            ],
        ]);
    }
}