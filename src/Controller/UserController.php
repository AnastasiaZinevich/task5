<?php



namespace App\Controller;

use App\Entity\Userf; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Faker\Factory as FakerFactory; 
use Symfony\Component\HttpFoundation\JsonResponse;
use League\Csv\Writer;


class UserController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request): Response
    {
       
        $region = $request->query->get('region');
        $errorCount = $request->query->get('error_count');
        $seed = $request->query->get('seed');
        $page = $request->query->getInt('page', 1);
        
        $combinedSeed = $this->generateCombinedSeed($seed, $page);
        
        $faker = \Faker\Factory::create();
        $faker->seed($combinedSeed);

       

       
        $errorCount = floatval($errorCount);

       
        if ($errorCount < 0 || $errorCount > 10) {
            throw new BadRequestHttpException('Error count must be between 0 and 10.');
        }

        
        $locale = $this->getLocaleFromRegion($region);
        $faker = \Faker\Factory::create($locale);

       
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 20);

       
        $users = [];
        for ($i = $offset; $i < $offset + $limit; $i++) {
           
            
            $user = [
                'id' => $faker->randomNumber(),
                'name' => $faker->name(),
                'address' => $faker->address(),
                'phone' => $faker->phoneNumber(),
            ];



$user = $this->simulateErrors($user, $errorCount, $locale);
           

           
            $users[] = $user;
        }

       
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['users' => $users]);
        }

       
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'region' => $region,
            'errorCount' => $errorCount,
            'seed' => $seed,
        ]);
    }

    
    private function getLocaleFromRegion($region)
    {
        switch ($region) {
            case 'USA':
                return 'en_US'; 
            case 'Poland':
                return 'pl_PL'; 
            case 'China':
                return 'zh_CN'; 
          
           
            case 'Russia':
                return 'ru_RU'; 
           case 'Korea':
                return 'ko_KR';
            case 'Georgia':
                return 'ka_GE';
            default:
                return 'en_US'; 
        }
    }

   
    private function simulateErrors($user, $errorCount, $locale)
    {
        
        $faker = \Faker\Factory::create($locale);
    
      
        for ($i = 0; $i < $errorCount; $i++) {
           
            $field = mt_rand(0, 2);
    
           
            switch ($field) {
                case 0:
                   
                    if (!empty($user['name'])) {
                        $user['name'] = $this->introduceError($user['name'], $faker);
                    }
                    break;
                case 1:
                  
                    if (!empty($user['address'])) {
                        $user['address'] = $this->introduceError($user['address'], $faker);
                    }
                    break;
                case 2:
                    
                    if (!empty($user['phone'])) {
                        $user['phone'] = $this->introduceError($user['phone'], $faker);
                    }
                    break;
            }
        }
    
        return $user;
    }
    
    private function introduceError($value, $faker)
{
   
    $errorType = mt_rand(0, 2);

   
    switch ($errorType) {
        case 0:
           
            $randomIndex = mt_rand(0, mb_strlen($value, 'UTF-8') - 1);
            $value = mb_substr($value, 0, $randomIndex) . mb_substr($value, $randomIndex + 1);
            break;
        case 1:
            
            $randomChar = $faker->randomLetter();
            $randomIndex = mt_rand(0, mb_strlen($value, 'UTF-8'));
            $value = mb_substr($value, 0, $randomIndex) . $randomChar . mb_substr($value, $randomIndex);
            break;
        case 2:
           
            $valueLength = mb_strlen($value, 'UTF-8');
            if ($valueLength >= 2) {
                $randomIndex = mt_rand(0, $valueLength - 2);
                $char1 = mb_substr($value, $randomIndex, 1);
                $char2 = mb_substr($value, $randomIndex + 1, 1);
                $value = mb_substr($value, 0, $randomIndex) . $char2 . $char1 . mb_substr($value, $randomIndex + 2);
            }
            break;
    }

    return $value;
}
private function generateCombinedSeed($seed, $page)
{
   
    $seed = intval($seed);
    $page = intval($page);

  
    $combinedSeed = $seed + $page;

    return $combinedSeed;
}
/**
 * @Route("/export-to-csv", name="export_to_csv")
 */
public function exportToCsv(Request $request): Response
    {
       
        $userData = json_decode($request->getContent(), true);

       
        $csvWriter = Writer::createFromString('');

      
        foreach ($userData as $user) {
            $csvWriter->insertOne([$user['id'], $user['name'], $user['address'], $user['phone']]);
        }

       
        $response = new Response($csvWriter->getContent(), Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="users.csv"');

        return $response;
    }
}