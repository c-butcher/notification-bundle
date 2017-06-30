# Notification Bundle

## Configuration
Below is an example of the basic configuration for this bundle.
```yaml

# KungFu Notifications
notification:
  mailer:
  
    # When you send a notification email, this is the name and email address
    # that it will be sent from.
    from:
      name: 'Your Site Name'
      address: 'example@yoursite.com'
      
    # When you send a notification email, this is the name and email address
    # that users will be able to reply to.
    reply_to:
      name: 'No Reply'
      address: 'no-reply@yoursite.com'
 
  # By supplying us with the user entity, along with the identifier and email properties on that entity,
  # we can associate our notification settings with your users, and send them emails.
  user:
    class: AppBundle\Entity\User
    properties:
    
      # The property which contains the unique identifier on your user entity.
      identifier: id
      
      # The property which contains the email address on your user entity.
      email: email
      
  # Below is a list of all the notifications that you want to setup.
  notifications:
  
    # This is the name/key for your notification. It is what you will use to identify
    # the notification within your code.
    product.min_quantity:
        
      # This is the subject of the email.
      subject: "Minimum Quantity Warning"
      
      # The description is used to explain to the user what this notification does.
      # It will show up on the notification settings page.
      description: "When a product reaches its minimum quantity."
      
      # This is the path to the email template which will be sent.
      template: "@Inventory/notices/min_quantity.html.twig"
      
      # The schedule determines how many seconds to wait before sending the email.
      schedule: 0
      
      # Tells whether users should be subscribed to this notification by default.
      enabled: true
```