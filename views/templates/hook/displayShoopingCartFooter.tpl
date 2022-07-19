{*
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
*}

<div class="empty-cart-container {if !empty($html_classes)}{$html_classes}{/if}"
	style="{if !empty($html_selector)}display:none;{/if}">

	{if !empty($show_modal)}
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#emptycart-modal">
			<i class="material-icons">delete</i>
			<span>{l s='Empty cart' mod='emptycart'}</span>
		</button>

		<div class="modal fade" id="emptycart-modal" tabindex="-1" role="dialog"
			aria-labelledby="emptycartLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<span>{l s='Are you sure you want to remove all the cart products?' mod='emptycart'}</span>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary"
							data-dismiss="modal">{l s='Close' mod='emptycart'}</button>
						<a href="{$cleancart_url}" class="btn btn-primary" role="button">
							{l s='Confirm' mod='emptycart'}
						</a>
					</div>
				</div>
			</div>
		</div>
	{else}
		<a href="{$cleancart_url}" class="btn btn-primary" role="button">
			<i class="material-icons">delete</i>
			<span>{l s='Empty cart' mod='emptycart'}</span>
		</a>
	{/if}
</div>