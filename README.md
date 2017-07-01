# Kung Fu Notification Bundle

This notification bundle allows you to easily manage user notifications. It allows users to choose which notifications
they want to receive, and then emails them whenever those notification are sent.

## Example Configuration
Below is an example of the basic configuration for this bundle.
```yaml
kungfu_notifications:
    mailer:
        from:
            name: 'Your Site Name'
            address: 'example@yoursite.com'
        reply_to:
            name: 'No Reply'
            address: 'no-reply@yoursite.com'
 
    user:
        class: AppBundle\Entity\User
        properties:
            identifier: id
            email: email
      
    notifications:
        product.min_quantity:
            subject: "Minimum Quantity Warning"
            description: "When a product reaches its minimum quantity."
            template: "@Inventory/notices/min_quantity.html.twig"
            enabled: true
```

## Adding a New Notification

In your symfony configuration, find the `kungfu_notifications` configuration, or you might have to create it. Once you
have that, then you'll want to add your new notification to the `notifications` section. The first property that you want
to add under the `notifications` section should be the name of the notification, in this case `product_min_quantity`.

```yaml
kungfu_notifications:
    notifications:
        product_min_quantity:
            subject: "Minimum Quantity Warning"
            description: "When a product reaches its minimum quantity."
            template: "@Inventory/notices/min_quantity.html.twig"
            enabled: true
```

**NOTE**: The name of the notification must start with a letter and only contain letters, numbers and underscores.

Your new notification will have four properties that you need to set, as they are all required.

* `subject` - will be used as the subject of the email.
* `description` - is used on the settings page to describe then notification to your end-users.
* `template` - a php/twig template that contains the contents of your email.
* `enabled` - tells whether the notification should be enabled by default.

___

Once your notification has been created, you'll next want to create the email template which will contain the contents
of your email notification. 

```html
<h3>Minimum Quanity Reached</h3>
<p>The {{ product.name }} product has reached its minimum quantity of {{ product.min_quantity }}.</p> 
```

That's it. Read the next section to find out how to send your notifications to your users.

## Sending Notifications
Now that you have your notifications configured, you can add a few simple lines of code in your controller to send the
notifications. The steps are as follows:

1. Load the users which you want to send the notification to.
2. Get the notifier service from the container.
3. Send the notification by supply three arguments.
   * Users you want to send the notification to.
   * The name of the notification.
   * The arguments which will be sent to the notification template.

```php
<?php
 
class ProductController extends Controller {
    public function indexAction(Product $product) {
        $users = $this->getCompanyUsers();
        
        $notifier = $this->get('notification.notifier');
        $notifier->send($users, 'product_min_quantity', array(
            'product' => $product
        ));
    }
}
 
?>
```

## Performance Tip
If your site slows down when notifications are sent, then we suggest you change the SwiftMailer spooling to use the
filesystem, and then process the spool using a cron job.

You can easily adjust SwiftMailer to use the filesystem by changing the SwiftMailer configuration.

```yaml
swiftmailer:
    spool:
        type: file
        path: "%kernel.project_dir%/var/spool"
```

**NOTE**: The spool path uses `%kernel.project_dir%` which points the path to your application root.

After changing the spool type to use the filesystem, you will then need to create a cron job that calls the following
command:

```
    /your_project_path/bin/console swiftmailer:spool:send
```

You will need to change `your_project_path` with the full directory structure leading to your Symfony project, and we
suggest that you execute the cron job 1-5 minutes depending on your needs.