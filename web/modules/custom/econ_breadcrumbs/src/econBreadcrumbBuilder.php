<?php

namespace Drupal\econ_breadcrumbs;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Link;
use Drupal\taxonomy\Entity\Term;

class econBreadcrumbBuilder implements BreadcrumbBuilderInterface {

	use LinkGeneratorTrait;
	use StringTranslationTrait;

	/**
	 * {@inheritdoc}
	 */
	public function applies(RouteMatchInterface $route_match) {

		$parameters = $route_match->getParameters()->all();
		if (isset($parameters['taxonomy_term'])) {

			$term = $parameters['taxonomy_term'];
			if ($term->getVocabularyId() == 'menu_topics') {
				return TRUE;
			}
		}
		return FALSE;	
	}

	/**
	 * {@inheritdoc}
	 */
	public function build(RouteMatchInterface $route_match) {

		$parameters = $route_match->getParameters()->all();
		if (isset($parameters['taxonomy_term'])) {

			$term = $parameters['taxonomy_term'];
			$breadcrumb = new \Drupal\Core\Breadcrumb\Breadcrumb();
			$breadcrumb->addCacheContexts(["url"]);
			$breadcrumb->addCacheTags(["taxonomy_term:{$term->id()}"]);
			$breadcrumb->addLink(Link::createFromRoute($term->getName(), '<none>'));	
		}

		$breadcrumb->addLink(Link::createFromRoute($this->t('Home'), '<front>'));
		return $breadcrumb;
	}
}
