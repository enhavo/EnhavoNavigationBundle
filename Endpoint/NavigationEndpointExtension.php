<?php

/*
 * This file is part of the enhavo package.
 *
 * (c) WE ARE INDEED GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enhavo\Bundle\NavigationBundle\Endpoint;

use Enhavo\Bundle\ApiBundle\Data\Data;
use Enhavo\Bundle\ApiBundle\Endpoint\AbstractEndpointTypeExtension;
use Enhavo\Bundle\ApiBundle\Endpoint\Context;
use Enhavo\Bundle\AppBundle\Endpoint\Type\ViewEndpointType;
use Enhavo\Bundle\NavigationBundle\Repository\NavigationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NavigationEndpointExtension extends AbstractEndpointTypeExtension
{
    public function __construct(
        private NavigationRepository $navigationRepository,
    ) {
    }

    public function handleRequest($options, Request $request, Data $data, Context $context)
    {
        if (!$options['navigation']) {
            return;
        }

        $navigations = $this->navigationRepository->findAll();

        $navigationData = [];
        foreach ($navigations as $navigation) {
            if (true === $options['navigation'] || (is_array($options['navigation']) && in_array($navigation->getCode(), $options['navigation']))) {
                $navigationData[$navigation->getCode()] = $this->normalize($navigation, null, ['groups' => ['endpoint.navigation']]);
            }
        }

        $data->set('navigation', $navigationData);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'navigation' => false,
        ]);
    }

    public static function getExtendedTypes(): array
    {
        return [ViewEndpointType::class];
    }
}
