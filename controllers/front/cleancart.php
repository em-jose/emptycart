<?php
/**
 * 2022
 *
 * This file is part of EmptyCart for prestashop.
 *
 * EmptyCart for prestashop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EmptyCart for prestashop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EmptyCart for prestashop.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jose María Escribano
 * @copyright 2022 Jose María Escribano
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
*/

class emptycartcleancartModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        $this->emptyCart();

        Tools::redirect('index.php?controller=cart&action=show');
    }

    public function emptyCart()
    {
        $context = Context::getContext();
        $cart_products = $context->cart->getProducts();

        if (empty($cart_products)) {
            return false;
        }

        foreach ($cart_products as $product) {
            $context->cart->deleteProduct($product['id_product'], $product['id_product_attribute']);
        }

        return true;
    }
}
