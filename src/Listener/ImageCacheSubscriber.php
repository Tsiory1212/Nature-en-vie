<?php
namespace App\Listener;

use App\Entity\Product;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Imagine\Gd\Image;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

// ICI ON TRAITE LES SUPPRESSION DES CACHES (Liip-imagine) via les évènements preRemove() et preUpdate()
// On peut servir aussi de cet évènement pour les autres persitances normaux

class ImageCacheSubscriber implements EventSubscriber {

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }


    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'preUpdate '
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // On va ajouter cette logique, car cet evenement va être déclanché pour tous les persistances 
        // On va le faire seulement pour l'entity Product
        if (!$entity instanceof Product ) {
            return;
        }

        $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));

    }

    public function preUpdate(LifecycleEventArgs  $args)
    {
        $entity = $args->getEntity();


        // On va ajouter cette logique, car cet evenement va être déclanché pour tous les persistances 
        // On va le faire seulement pour l'entity Product
        if (!$entity instanceof Product ) {
            return;
        }

        // if ($entity instanceof UploadedFile ) {
    
            // $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
        // }


    }
}
