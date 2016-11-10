<?php

namespace OGAMBundle\Repository\Metadata;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use OGAMBundle\Entity\Metadata\FileField;

/**
 * FileFormatRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FileFormatRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get a File Format object by its format.
     *
     * @param String $fileFormat
     *            The file format
     * @param String $locale
     *            The locale
     * @return FileFormat
     */
    public function getFileFormat($fileFormat, $locale) {
        
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('OGAMBundle\Entity\Metadata\FileFormat', 'ffo');
        
        $sql = " SELECT format, type, file_extension, file_type, position, COALESCE(t.label, file_format.label) as label ";
        $sql .= " FROM file_format ";
        $sql .= " JOIN format using (format) ";
        $sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'FILE_FORMAT' AND row_pk = format) ";
        $sql .= " WHERE format = :fileFormat ";
    
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters([
            'lang' => $locale,
            'fileFormat' => $fileFormat
        ]);
        
        $fileFormat = $query->getSingleResult();
    
        // Fill each form with its fields
        $fileFormat->setFields($this->_em->getRepository(FileField::class)->getFileFields($fileFormat->getFormat()));
    
        return $fileFormat;
    }
    
    /**
     * Get the files used by a dataset.
     *
     * @param String $datasetId
     *            The identifier of the dataset
     * @param String $locale
     *            The locale
     * @return Array[FileFormat]
     */
    public function getFileFormats($datasetId, $locale)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('OGAMBundle\Entity\Metadata\FileFormat', 'ffo');
        
        $sql = " SELECT format, type, file_extension, file_type, position, COALESCE(t.label, file_format.label) as label ";
        $sql .= " FROM dataset_files";
        $sql .= " LEFT JOIN translation t ON (lang = :lang AND table_format = 'FILE_FORMAT' AND row_pk = format)";
        $sql .= " LEFT JOIN file_format using (format) ";
        $sql .= " LEFT JOIN format using (format) ";
        $sql .= " WHERE dataset_id = :datasetId ";
        $sql .= " ORDER BY position";
        
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters([
            'lang' => $locale,
            'datasetId' => $datasetId
        ]);
    
        $fileFormats = $query->getResult();
    
        // Fill each form with its fields
        foreach($fileFormats as $form){
            $form->setFields($this->_em->getRepository(FileField::class)->getFileFields($form->getFormat()));
        }
    
        return $fileFormats;
    }
}
