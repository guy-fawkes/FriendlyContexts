Feature: Alice and Entity Context
  In order to manage my fixtures efficiently
  As a developer
  I need to be able to to associate Alice created fixtures with inline fixtures

  Background:
    Given a file named "src/Knp/FcTestBundle/Entity/User.php" with:
      """
      <?php
      namespace Knp\FcTestBundle\Entity;

      use Doctrine\Common\Collections\ArrayCollection;
      use Doctrine\ORM\Mapping as ORM;

      /**
       * @ORM\Entity
       */
      class User
      {
        /**
         * @ORM\Column(type="bigint")
         * @ORM\Id
         * @ORM\GeneratedValue
         */
        public $id;

        /**
         * @ORM\Column
         */
        public $login;

        /**
         * @ORM\Column
         */
        public $name;
      }
      """
    And a file named "src/Knp/FcTestBundle/Entity/Product.php" with:
      """
      <?php
      namespace Knp\FcTestBundle\Entity;

      use Doctrine\Common\Collections\ArrayCollection;
      use Doctrine\ORM\Mapping as ORM;

      /**
       * @ORM\Entity
       */
      class Product
      {
          /**
           * @ORM\Column(type="bigint")
           * @ORM\Id
           * @ORM\GeneratedValue
           */
          public $id;

          /**
           * @ORM\Column
           */
          public $Name;

          /**
           * @ORM\Column(type="decimal")
           */
          public $Price;

          /**
           * @ORM\ManyToOne(targetEntity="User")
           */
          public $User;
      }
      """
    And I have the following behat configuration:
      """
      default:
        formatters:
          progress:
            paths: false
        extensions:
          Behat\Symfony2Extension: ~
          Behat\MinkExtension:
            default_session: 'symfony2'
            sessions:
              symfony2:
                symfony2: ~
          Knp\FriendlyContexts\Extension:
            alice:
              fixtures:
                Users: features/fixtures/users.yml
        suites:
          simple:
            type: symfony_bundle
            bundle: KnpFcTestBundle
            contexts:
              - FeatureContext: ~
              - Behat\MinkExtension\Context\MinkContext: ~
              - Knp\FriendlyContexts\Context\AliceContext: ~
              - Knp\FriendlyContexts\Context\EntityContext: ~
      """
    And a file named "features/fixtures/users.yml" with:
      """
      Knp\FcTestBundle\Entity\User:
        user-john:
          login: john.doe
          name: John Doe
        user-admin:
          login: admin
          name: Admin
      """

  Scenario: I can reference Alice fixtures when creating additional fixtures inline
    Given a file named "src/Knp/FcTestBundle/Features/list-users.feature" with:
      """
      @alice(Users)
      Feature: list products
        In order to manage products
        As an admin
        I need to see a list of products and who created them

        Scenario:
          Given the following products:
            | name   | price  | user     |
            | Phone  | 698.95 | admin    |
            | Tablet | 890.95 | john.doe |
            | TV     | 390.00 | john.doe |
          And I am on the homepage
          Then I should see "Phone created by Admin"
          Then I should see "Tablet created by John Doe"
          And I should see "TV created by John Doe"
      """
    And a file named "src/Knp/FcTestBundle/Controller/DefaultController.php" with:
      """
      <?php
      namespace Knp\FcTestBundle\Controller;

      use Knp\FcTestBundle\Entity\Product;
      use Symfony\Bundle\FrameworkBundle\Controller\Controller;

      class DefaultController extends Controller
      {
        public function indexAction()
        {
          return $this->render('@KnpFcTest/Default/index.html.twig', array(
            'products' => $this->container->get('doctrine')->getManager()->getRepository(Product::class)->findBy(array()),
          ));
        }
      }
      """
    And a file named "src/Knp/FcTestBundle/Resources/views/Default/index.html.twig" with:
      """
      <!DOCTYPE html>
      <html>
        <head></head>
        <body>
          <h1>Users</h1>
          <ul>
          {% for product in products %}
            <li>{{ product.Name }} created by {{ product.User.name }}</li>
          {% endfor %}
          </ul>
        </body>
      </html>
      """
    When I run "behat -vvv --no-colors -f progress"
    Then it should pass with:
      """
      .....

      1 scenario (1 passed)
      5 steps (5 passed)
      """
