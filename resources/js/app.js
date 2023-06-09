import '../scss/app.scss';
import 'bootstrap-icons/font/bootstrap-icons.css';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import $ from 'jquery';
window.$ = $;

import './flash-message';

$(window).on('load', () => {
  // sidebar toggler
  const hamburgerMenuIcon = $('.hamburger-menu-icon');
  const sidebar = $('.sidebar');

  hamburgerMenuIcon.on('click', () => {
    sidebar.toggleClass('active');
  });

  // sidebar sub-menu toggler
  const subMenuItems = $('.sidebar-menu > li:not(.sidebar-menu-title)');
  $.each(subMenuItems, (idx, subMenuItem) => {
    $(subMenuItem).on('click', (e) => {
      const subMenu = $(subMenuItem).find('ul');
      if (subMenu.css('max-height') != '0px') {
        subMenu.css('max-height', '0px').removeClass('my-1');
      } else {
        subMenu.css('max-height', subMenu.prop('scrollHeight') + 'px').addClass('my-1');
      }
    });
  });

  // inc, dec number input
  const numInpDecBtns = $('.num-inp-dec-btn');
  const numInpIncBtns = $('.num-inp-inc-btn');

  $.each(numInpDecBtns, (idx, numInpDecBtn) => {
    $(numInpDecBtn).on('click', (e) => {
      const numInpCurVal = $(e.target).next().val();
      const minNumAllowed = $(e.target).next().attr('min') || Number.NEGATIVE_INFINITY;
      $(e.target).next().val(Math.max(numInpCurVal - 1, minNumAllowed));
    });
  });

  $.each(numInpIncBtns, (idx, numInpIncBtn) => {
    $(numInpIncBtn).on('click', (e) => {
      const numInpCurVal = $(e.target).prev().val();
      const maxNumAllowed = $(e.target).prev().attr('max') || Number.POSITIVE_INFINITY;
      $(e.target).prev().val(Math.min(Number(numInpCurVal) + 1, maxNumAllowed));
    });
  });
});

