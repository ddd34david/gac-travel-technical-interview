<?php

namespace App\EventListener;

use App\Entity\Product;
use App\Entity\StockHistoric;
use Symfony\Component\Security\Core\Security;

use Doctrine\Persistence\Event\LifecycleEventArgs;


class ProductChangedStock
{
    public function __construct(private Security $security)
    {
    }

    public function postUpdate(Product $product, LifecycleEventArgs $event): void
    {
        $entityManager = $event->getObjectManager();
        $cambiosUpdate = $entityManager->getUnitOfWork()->getEntityChangeSet($product);
        if(array_key_exists('stock',$cambiosUpdate)){
            $old = $cambiosUpdate['stock'][0];
            $new = $cambiosUpdate['stock'][1];
            
            $stockHistoric = new StockHistoric();
            $stockHistoric->setMyUser($this->security->getUser());
            $stockHistoric->setProduct($product);
            $stockHistoric->setStock($new - $old);
            
            
            $entityManager->persist($stockHistoric);
            $entityManager->flush();
        }
    }
}

