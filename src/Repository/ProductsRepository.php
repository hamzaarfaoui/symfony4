<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }
    public function findOneByQB($slug)
    {
        $qb = $this->createQueryBuilder('u')
                ->Select('u.id', 'u.name', 'u.price', 'u.image', 'u.qte', 'u.pricePromotion')
                ->where('u.slug = :slug')
                ->setParameter(':slug', $slug);

        return $qb->getQuery()->execute();
    }

    public function findByQB($sousCategorie)
    {
        $qb = $this->createQueryBuilder('u')
                ->Select('u.name', 'u.price', 'u.image', 'u.qte', 'u.pricePromotion', 'u.slug')
                ->leftJoin('u.sousCategorie', 'sc')
                ->where('sc.slug = :sousCategorie')
                ->setParameter(':sousCategorie', $sousCategorie);

        return $qb->getQuery()->execute();
    }

    public function findArray($array)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->add('where', $qb->expr()->in('u.id', ':my_array'))
        ->setParameter('my_array', $array);

        return $qb->getQuery()->execute();
    }
    
    public function produitsLiees($params)
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.slug != :slug')
                ->andWhere('u.sousCategorie = :sousCategorie')
                ->setMaxResults(4)
                ->setParameter('slug', $params['slug'])
                ->setParameter('sousCategorie', $params['sousCategorie']);

        return $qb->getQuery()->execute();
    }
    
    public function byQB($params, $disponible)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where('u.name LIKE :name')
                ->setParameter('name', '%'.$params.'%');
        $this->addFilters($qb, $disponible);
        return $qb->getQuery()->execute();
    }
    
    private function addFilters($qb, $params)
    {
        if($params == 'oui'){
            $qb->andWhere('u.qte > 0');
        }
        
        
       return $qb;
    }
    
    public function newProducts()
    {
        $qb = $this->createQueryBuilder('u')
                ->orderBy('u.createdAt', 'desc');
        return $qb->getQuery()->execute();
    }
    
    public function venteFlash()
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.pricePromotion > 0')
                ->orderBy('u.createdAt', 'desc');
        return $qb->getQuery()->execute();
    }
    
    public function inPromotion()
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.pricePromotion > 0')
                ->orderBy('u.createdAt', 'desc');
        return $qb->getQuery()->execute();
    }
    
    public function byNbrViews($store)
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.store = :store')
                ->orderBy('u.nbrView', 'desc')
                ->andWhere('u.nbrView > 0')
                ->setMaxResults(6)
                ->setParameter('store', $store);
        return $qb->getQuery()->execute();
    }
    /**
    on est arrivé içi
    */
    public function byNbrAddToCart($store)
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.store = :store')
                ->andWhere('u.nbrAddToCart > 0')
                ->orderBy('u.nbrAddToCart', 'desc')
                ->setMaxResults(6)
                ->setParameter('store', $store);
        return $qb->getQuery()->execute();
    }
    
    public function byNbrAddToFavorite($store)
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.store = :store')
                ->andWhere('u.nbrAddToFavorite > 0')
                ->orderBy('u.nbrAddToFavorite', 'desc')
                ->setMaxResults(6)
                ->setParameter('store', $store);
        return $qb->getQuery()->execute();
    }
    
    public function byPosAndSc($sousCategorie)
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.sousCategorie = :sc')
                ->orderBy('u.position', 'ASC')
                ->setParameter('sc', $sousCategorie);
        return $qb->getQuery()->execute();
    }
    
    public function liees($sousCategorie)
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.sousCategorie = :sc')
                ->orderBy('u.price', 'ASC')
                ->setParameter('sc', $sousCategorie);
        return $qb->getQuery()->execute();
    }
    
    public function byStore($store)
    {
        $qb = $this->createQueryBuilder('u')
                ->where('u.store = :store')
                ->orderBy('u.createdAt', 'desc')
                ->setParameter('store', $store);
        return $qb->getQuery()->execute();
    }
    
    public function byCategorie($params)
    {
        $qb = $this->createQueryBuilder('u');
            $qb->where('u.sousCategorie = :sc')
            ->leftJoin('u.valeurs', 'v')
            ->setParameter('sc', $params['categorie']);    
            
        if ((isset($params['minimum']) && !empty($params['minimum'])) && (isset($params['minimum'])&&!empty($params['minimum']))){
            $qb->andWhere($qb->expr()->between('u.pricePromotion', $params['minimum'],$params['maximum']));
        }

        if(isset($params['list'])){
            $qb->andWhere('u.marque IN (:marques)')
            ->setParameter('marques', $params['marques']);
        }
        if(isset($params['tri'])&&!empty($params['tri'])){
            if ($params['tri'] == 1){
                $qb->orderBy('u.pricePromotion', 'DESC');
            }elseif ($params['tri'] == 2){
                $qb->orderBy('u.pricePromotion', 'ASC');
            }elseif ($params['tri'] == 3){
                $qb->orderBy('u.nbrView', 'DESC');
            }
        }else{

            $qb->orderBy('u.position', 'ASC');
        }
        if(isset($params['marques'])){
            $qb->andWhere('u.marque IN (:marques)')
            ->setParameter('marques', $params['marques']);
        }
        if(isset($params['couleurs'])){
            $qb->andWhere('u.couleur IN (:couleurs)')
            ->setParameter('couleurs', $params['couleurs']);
        }
        if(isset($params['valeurs'])){
            $qb->andWhere('v.id IN (:valeurs)')
            ->setParameter('valeurs', $params['valeurs']);
        }
        if(isset($params['store'])&&!empty($params['store'])){
            $qb->andWhere('u.store = :store')
            ->setParameter('store', $params['store']);
        }
        return $qb->getQuery()->execute();
    }
    public function listProductsBycategories($categorie, $limit = 0)
    {
        $qb = $this->createQueryBuilder('u');
            $qb
            ->Select('u.id', 'u.name', 'u.slug', 'u.price', 'u.pricePromotion', 'u.image', 'u.qte', 'u.createdAt', 's.name AS store_name')
            ->leftJoin('u.store', 's')
           ->where('u.sousCategorie = :sc')
            ->orderBy('u.position', 'ASC');
            if($limit>0){
                $qb->setMaxResults($limit);
            }
            $qb->setParameter('sc', $categorie);    
            
        
        return $qb->getQuery()->execute();
    }

    public function marquesProductsBycategories($categorie)
    {
        $qb = $this->createQueryBuilder('u');
            $qb
            ->Select('m.id', 'm.name')
            ->leftJoin('u.marque', 'm')
            ->where('u.sousCategorie = :sc')
            ->groupBy('m.id')
            ->setParameter('sc', $categorie);    
            
        
        return $qb->getQuery()->execute();
    }


    public function couleursProductsBycategories($categorie)
    {
        $qb = $this->createQueryBuilder('u');
            $qb
            ->Select('c.id', 'c.code')
            ->leftJoin('u.couleur', 'c')
            ->where('u.sousCategorie = :sc')
            ->andWhere('c.code IS NOT NULL')
            ->groupBy('c.id')
            ->setParameter('sc', $categorie);    
            
        
        return $qb->getQuery()->execute();
    }
    
    
    
    public function byKeyword($keywords)
    {
        $qb = $this->createQueryBuilder('u')
                ->select('u.id', 'u.name', 'u.slug', 'u.image', 'u.qte', 'u.price', 'u.pricePromotion', 'u.createdAt')
                ->leftJoin('u.keywords', 'k')
                ->where('k.id IN (:keywords)')
                ->andWhere('u.qte > 0')
                ->orderBy('u.createdAt', 'desc')
                ->setParameter('keywords', $keywords);
        return $qb->getQuery()->execute();
    }
    public function produitsCriteres($id)
    {
        $qb = $this->createQueryBuilder('u')
                ->Select('c.id', 'c.name')
                ->leftJoin('u.valeurs', 'v')
                ->leftJoin('v.caracteristique', 'c')
                ->where('u.id = :id')
                ->groupBy('c.id')
                ->setParameter('id', $id);
        return $qb->getQuery()->execute();
    }
    public function BannerCriteres($list)
    {
        $qb = $this->createQueryBuilder('u')
                ->Select('c.id', 'c.name')
                ->leftJoin('u.valeurs', 'v')
                ->leftJoin('v.caracteristique', 'c')
                ->where('u.id IN (:list)')
                ->andWhere('c.code IS NOT NULL')
                ->groupBy('c.id')
                ->setParameter('list', $list);
        return $qb->getQuery()->execute();
    }
    public function BannerMarques($list)
    {
        $qb = $this->createQueryBuilder('u')
                ->Select('m.id', 'm.name')
                ->leftJoin('u.marque', 'm')
                ->where('u.id IN (:list)')
                ->groupBy('m.id')
                ->setParameter('list', $list);
        return $qb->getQuery()->execute();
    }

    public function BannerCouleurs($list)
    {
        $qb = $this->createQueryBuilder('u')
                ->Select('c.id', 'c.code')
                ->leftJoin('u.couleur', 'c')
                ->where('u.id IN (:list)')
                ->groupBy('c.id')
                ->setParameter('list', $list);
        return $qb->getQuery()->execute();
    }

    
//    public function byPriceAsc()
//    {
//        $qb = $this->createQueryBuilder('Products')
//                ->orderBy('price', 'asc');
//        return $qb->getQuery()->execute();
//    }
//    
//    public function byPulaires()
//    {
//        $qb = $this->createQueryBuilder('Products')
//                ->orderBy('nbrView', 'desc');
//        return $qb->getQuery()->execute();
//    }
    
    // /**
    //  * @return Products[] Returns an array of Products objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Products
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
