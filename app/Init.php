<?php

namespace GetepayCF7;

final class Init
{
  /**
   * Returns an array of services to be registered.
   *
   * @return array The array of services to be registered.
   */
  public static function get_services()
  {
    return array(
      Admin\Menu::class,
      Admin\BarMenu::class,
      Admin\ActionLink::class,
      Admin\EnqueueScripts::class,
      Settings\API::class,
      Settings\General::class,
      Settings\Email::class,
      Settings\Validation::class,
      Payment\ProcessRedirect::class,
      Payment\CallbackHandler::class,
      Payment\FormSubmission::class,
    );
  }

  /**
   * Registers all the services returned by get_services().
   */
  public static function register_services()
  {
    if (!function_exists('is_plugin_active')) {
      include_once(ABSPATH . 'wp-admin/includes/plugin.php');

      if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
        add_action("admin_notices", function () {
          echo sprintf(
            '<div class="notice notice-error">
              <p><strong>Getepay for Contact Form 7 -</strong> Contact Form 7 is not installed and activated. Please install and activate the plugin.</p>
            </div>'
          );
        });
        return;
      }
    }

    foreach (self::get_services() as $class) {
      $service = self::instantiate($class);
      if (method_exists($service, 'register')) {
        $service->register();
      }
    }
  }

  /**
   * Instantiates the provided class.
   *
   * @param string $class The class to be instantiated.
   *
   * @return object The instance of the provided class.
   */
  private static function instantiate($class)
  {
    return new $class;
  }
}
