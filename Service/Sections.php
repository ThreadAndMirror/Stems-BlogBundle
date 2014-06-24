<?php

namespace Stems\BlogBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormFactoryInterface;

/**
 *	Handles the rendering, processing and other functionality of blog post sections to prevent controller bloat
 */
class Sections
{
	// the entity manager
	protected $em;

	// the twig renderer
	protected $twig;

	// the form builder
	protected $formFactory;

	// any errors generated by the save methods
	protected $saveErrors = array();

	public function __construct(EntityManager $em, TwigEngine $twig, FormFactoryInterface $formFactory)
	{
		// load the dependency services
		$this->em = $em;
		$this->twig = $twig;
		$this->formFactory = $formFactory;
	}

	/**
	 *	returns an array of admin edit form renders for the provided collection of post section link entities
	 */
	public function getEditors($links=array())
	{
		$forms = array();

		// get the section forms
		foreach ($links as $link) {
			// the specific section data and render the form view
			$section = $this->em->getRepository('StemsBlogBundle:'.$link->getType()->getClass())->find($link->getEntity());
			// render the form view and store the html
			$forms[] = $section->editor($this, $link);
		}

		return $forms;
	}

	/**
	 * Builds and returns the specific form object for the requested section
	 */
	public function createSectionForm($link, $section)
	{
		// build the class name using the section type then create the form object
        $formClass = 'Stems\\BlogBundle\\Form\\'.$link->getType()->getClass().'Type';
        $form = $this->formFactory->create(new $formClass($link), $section);

        return $form;
	}

	/**
	 * Builds and returns the specific form object for the requested sub section (eg. scrapbook image/text)
	 */
	public function createSubSectionForm($section)
	{
		// build the class name using the section type then create the form object
		$entityClass = explode('\\Entity\\', get_class($section));
        $formClass = 'Stems\\BlogBundle\\Form\\'.end($entityClass).'Type';
        $form = $this->formFactory->create(new $formClass($section), $section);

        return $form;
	}

	/**
	 * Dynamic handler for posted form data when saving post sections
	 */
	public function saveSection($link, $parameters, $request)
	{
		try
		{
			// the specific section data and run the save
			$section = $this->em->getRepository('StemsBlogBundle:'.$link->getType()->getClass())->find($link->getEntity());
			$section->save($this, $parameters, $request, $link);
		}
		catch(\Exception $e)
        {
            // add an error message if the was a problem saving the section data
            $this->saveErrors[] = 'There was a problem saving section ID '.$link->getID().': '.$e->getMessage();
        }
	}

	/**
	 * Renders the front end html for a section
	 */
	public function renderSection($link)
	{
		// get the specific section instance data and run the renderer
		$section = $this->em->getRepository('StemsBlogBundle:'.$link->getType()->getClass())->find($link->getEntity());
		return $section->render($this, $link);
	}

	/**
	 * Get the entity manager
	 */
	public function getManager()
	{
		return $this->em;
	}

	/**
	 * Get twig
	 */
	public function getTwig()
	{
		return $this->twig;
	}

	/**
	 * Get any save errors
	 */
	public function getSaveErrors()
	{
		return $this->saveErrors;
	}
}
