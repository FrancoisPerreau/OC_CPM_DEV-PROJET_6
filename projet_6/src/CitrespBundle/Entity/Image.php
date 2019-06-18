<?php

namespace CitrespBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="CitrespBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=255, unique=false)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;


    private $file;

    // On ajoute cet attribut pour y stocker le nom du fichier temporairement
    private $tempFilename;




    // METHODES *************************
    // **********************************
    // public function upload()
    // {
    //     // Si il n'y a pas de fichier uploader on ne fait rien
    //     if ($this->file === null) {
    //         return;
    //     }
    //
    //     // On récupère le nom du fichier d'origine
    //     $name = $this->file->getClientOriginalName();
    //
    //     // On déplace le fichier dans notre répertoire
    //     $this->file->move($this->getUploadRootDir(), $name);
    //
    //     // On sauvegarde le nom de fichier dans notre attribut $extension
    //     $this->extension = $name;
    //
    //     // On crée également le futur attribut alt de notre balise <img>
    //     $this->alt = $name;
    // }
    //
    // public function getUploadDir()
    // {
    //     // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
    //     return 'img/uploads';
    // }
    //
    // protected function getUploadRootDir()
    // {
    //     // On retourne le chemin relatif vers l'image pour notre code PHP
    //     return __DIR__.'/../../../../web/'.$this->getUploadDir();
    // }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
      public function preUpload()
      {
        // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
        if ($this->file === null) {
          return;
        }

        // Le nom du fichier est son id, on doit juste stocker également son extension
        // Pour faire propre, on devrait renommer cet attribut en « extension », plutôt que « extension »
        $this->extension = $this->file->guessExtension();

        // Et on génère l'attribut alt de la balise <img>, à la valeur du nom du fichier sur le PC de l'internaute
        $this->alt = $this->file->getClientOriginalName();
      }

      /**
       * @ORM\PostPersist()
       * @ORM\PostUpdate()
       */
      public function upload()
      {
        // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
        if ($this->file === null) {
          return;
        }

        // Si on avait un ancien fichier, on le supprime
        if (null !== $this->tempFilename) {
          $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
          if (file_exists($oldFile)) {
            unlink($oldFile);
          }
        }
        // dump($this->file);
        // die;
        // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move(
          $this->getUploadRootDir(), // Le répertoire de destination
          $this->id.'-report.'.$this->extension   // Le nom du fichier à créer, ici « id.extension »
        );
        // dump($this->file);
        // die;
      }

      /**
       * @ORM\PreRemove()
       */
      public function preRemoveUpload()
      {
        // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->extension;
      }

      /**
       * @ORM\PostRemove()
       */
      public function removeUpload()
      {
        // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
        if (file_exists($this->tempFilename)) {
          // On supprime le fichier
          unlink($this->tempFilename);
        }
      }

      public function getUploadDir()
      {
        // On retourne le chemin relatif vers l'image pour un navigateur
        return 'theme/img/uploads';
      }

      protected function getUploadRootDir()
      {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return __DIR__ . '/../../../web/' . $this->getUploadDir();
      }


      public function getWebPath()
      {
        return $this->getUploadDir().'/'.$this->getId().'-report.'.$this->getExtension();
      }




    // GETTERS & SETTERS ****************
    // **********************************

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set extension.
     *
     * @param string $extension
     *
     * @return Image
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set alt.
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt.
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }



    public function getFile()
    {
      return $this->file;
    }

    public function setFile(UploadedFile $file)
    {
      $this->file = $file;

      // On vérifie si on avait déjà un fichier pour cette entité
      if (null !== $this->extension) {
          // On sauvegarde l'extension du fichier pour le supprimer plus tard
          $this->tempFilename = $this->extension;

          // On réinitialise les valeurs des attributs extension et alt
          $this->extension = null;
          $this->alt = null;
      }
    }
}
