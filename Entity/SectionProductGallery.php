<?php
namespace Stems\BlogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;

use Stems\BlogBundle\Definition\SectionInstanceInterface;

/** 
 * @ORM\Entity
 * @ORM\Table(name="stm_blog_section_productgallery")
 */
class SectionProductGallery implements SectionInstanceInterface
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** 
     * @ORM\Column(type="text", nullable=true)
     */
    protected $heading;

    /** 
     * @ORM\Column(type="text", nullable=true)
     */
    protected $caption;

    /** 
     * @ORM\Column(type="text")
     */
    protected $style = 'carousel';

    /**
     * @ORM\OneToMany(targetEntity="SectionProductGalleryProduct", mappedBy="sectionProductGallery")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $products;

    /**
     * Build the html for rendering in the front end, using any nessary custom code
     */
    public function render($services, $link)
    {
        // render the twig template
        return $services->getTwig()->render('StemsBlogBundle:Section:productGallery.html.twig', array(
            'section'   => $this,
            'link'      => $link,
        ));
    }

    /**
     * Build the html for admin editor form
     */
    public function editor($services, $link)
    {
        // build the section from using the generic builder method
        $form = $services->createSectionForm($link, $this);

        // render the admin form html
        return $services->getTwig()->render('StemsBlogBundle:Section:productGalleryForm.html.twig', array(
            'form'      => $form->createView(),
            'link'      => $link,
            'section'   => $this,
        ));
    }

    /**
     * Update the section from posted data
     */
    public function save($services, $parameters, $request, $link)
    {
        // save the values
        $this->setHeading(stripslashes($parameters['heading']));
        $this->setCaption(stripslashes($parameters['caption']));
        $this->setStyle($parameters['style']);

        // remove previously attached product images
        $this->clearProducts();

        $position = 1;

        // gather the new product images and append them to the gallery
        foreach ($parameters['products'] as $id) {
            $product = $services->getManager()->getRepository('StemsBlogBundle:SectionProductGalleryProduct')->find($id);
            $product->setSectionProductGallery($this);
            $product->setPosition($position);

            $services->getManager()->persist($product);
            $position++;

            // link the actual product to the article for the 'featured in' block
            $productEntity = $services->getManager()->getRepository('ThreadAndMirrorProductsBundle:Product')->find($product->getPid());
            $productEntity->setFeature($link->getPost()->getId());
            $services->getManager()->persist($productEntity);
        }
        
        $services->getManager()->persist($this);        
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set heading
     *
     * @param string $heading
     * @return Section
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;
    
        return $this;
    }

    /**
     * Get heading
     *
     * @return string 
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * Set caption
     *
     * @param string $caption
     * @return Section
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    
        return $this;
    }

    /**
     * Get caption
     *
     * @return string 
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set style
     *
     * @param string $style
     * @return Section
     */
    public function setStyle($style)
    {
        $this->style = $style;
    
        return $this;
    }

    /**
     * Get style
     *
     * @return string 
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Add products
     *
     * @param \Stems\BlogBundle\Entity\SectionProductGalleryProduct $products
     * @return SectionScrapbook
     */
    public function addProduct(\Stems\BlogBundle\Entity\SectionProductGalleryProduct $product)
    {
        $this->products[] = $product;
    
        return $this;
    }

    /**
     * Remove products
     *
     * @param \Stems\BlogBundle\Entity\SectionProductGalleryProduct $product
     */
    public function removeProduct(\Stems\BlogBundle\Entity\SectionProductGalleryProduct $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Remove all products
     */
    public function clearProducts()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getproducts()
    {
        return $this->products;
    }
}