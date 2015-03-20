<?php

namespace Stems\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class WidgetController extends Controller
{
	/**
	 * Renders the latest blog post
	 */
	public function latestPostAction()
	{
		// Get the latest blog post
		$em    = $this->getDoctrine()->getManager();
		$posts = $em->getRepository('StemsBlogBundle:Post')->findLatestForWidget(1);

		return $this->render('StemsBlogBundle:Widget:latestPost.html.twig', array(
			'post' 	=> reset($posts),
		));
	}

	/**
	 * Renders a (unpaginated) list of the most recent posts, defaulting to 5 if no limit is set
	 */
	public function latestPostsSidebarAction($limit = 4)
	{
		// Get the latest blog post
		$em    = $this->getDoctrine()->getManager();
		$posts = $em->getRepository('StemsBlogBundle:Post')->findLatestForWidget($limit);

		return $this->render('StemsBlogBundle:Widget:latestPostsSidebar.html.twig', array(
			'posts' 	=> $posts,
		));
	}

	/**
	 * Renders a specific blog post
	 */
	public function featurePostAction($id)
	{
		// Get the blog post
		$em   = $this->getDoctrine()->getManager();
		$post = $em->getRepository('StemsBlogBundle:Post')->find($id);

		return $this->render('StemsBlogBundle:Widget:latestPost.html.twig', array(
			'post' 	=> $post,
		));
	}

	/**
	 * Renders a blog post that features a product
	 */
	public function featuredInAction($id)
	{
		// Get the blog post
		$em   = $this->getDoctrine()->getManager();
		$post = $em->getRepository('StemsBlogBundle:Post')->find($id);

		return $this->render('StemsBlogBundle:Widget:featuredIn.html.twig', array(
			'post' 	=> $post,
		));
	}

	/**
	 * Renders the latest blog posts for the feature block
	 */
	public function homepageFeatureAction()
	{
		// Get the latest blog post
		$em    = $this->getDoctrine()->getManager();
		$posts = $em->getRepository('StemsBlogBundle:Post')->findLatestForWidget(5);

		return $this->render('StemsBlogBundle:Widget:homepageFeature.html.twig', array(
			'posts' 	=> $posts,
		));
	}
}
