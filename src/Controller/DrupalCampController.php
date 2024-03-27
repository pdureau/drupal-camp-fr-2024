<?php

declare(strict_types=1);

namespace Drupal\drupal_camp\Controller;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\HtmlResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for DDD routes.
 */
final class DrupalCampController extends ControllerBase {

  /**
   * Builds the original page.
   */
  public function original(): Response {
    $file_path = dirname(__FILE__) . "/original.yml";
    $build = Yaml::decode(file_get_contents($file_path));
    $response = $this->renderPage($build);
    return $response;
  }

  /**
   * Builds the revised page.
   */
  public function revised(): Response {
    $file_path = dirname(__FILE__) . "/revised.yml";
    $build = Yaml::decode(file_get_contents($file_path));
    $response = $this->renderPage($build);
    return $response;
  }

  /**
   * Render a page from a renderable array.
   */
  protected function renderPage(array $build): HtmlResponse {
    $start = microtime(TRUE);
    $html = [
      '#type' => 'html',
      'page' => $build,
    ];

    // Add the bare minimum of attachments from the system module and the
    // current maintenance theme.
    system_page_attachments($html['page']);
    \Drupal::service('renderer')->renderRoot($html);

    $response = new HtmlResponse();
    $response->setContent($html);
    // Process attachments, because this does not go via the regular render
    // pipeline, but will be sent directly.
    $response = \Drupal::service('html_response.attachments_processor')->processAttachments($response);
    print(microtime(TRUE) - $start);
    return $response;
  }

}
