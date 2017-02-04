<?php
/**
 * Created by PhpStorm.
 * User: dhawal
 * Date: 2/3/17
 * Time: 7:16 PM
 */

namespace ClassCentral\SiteBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Automatically tags courses based on metadata
 * @package ClassCentral\SiteBundle\Command
 */
class AutoTagCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('classcentral:tags:auto');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $tagService = $this->getContainer()->get('tag');


        $courses = $em->getRepository('ClassCentralSiteBundle:Course')->findAll();
        $count = 0;
        foreach ($courses as $course)
        {
            $tags = array();
            // Add Tags based on the courses country and institute
            foreach ($course->getInstitutions() as $ins)
            {
                if($ins->getCountry())
                {
                    $tags[] = $ins->getCountry();
                }

                if($ins->getContinent())
                {
                    $tags[] = $ins->getContinent();
                }
            }

            if(!empty($tags))
            {
                $tagService->saveCourseTags($course,$tags);
            }

            $count++;
            if($count%100 == 0)
            {
                $output->writeln("$count courses tagged");
            }

        }
    }
}