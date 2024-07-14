/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

import './styles/app.scss';
//import './bootstrap';

const btn = document.getElementById('menu-btn')
const menu = document.getElementById('menu')

btn.addEventListener('click', navToggle)

function navToggle() {
  btn.classList.toggle('open')
  menu.classList.toggle('flex')
  menu.classList.toggle('hidden')
}

// Icons
// const sunIcon = document.querySelector(".sun");
// const moonIcon = document.querySelector(".moon");

// //Theme Vars

// const userTheme = localStorage.getItem("theme");

// //Icon Toggling

// const iconToggle = () => {
//     moonIcon.classList.toggle("display-none");
//     sunIcon.classList.toggle("display-none"); 
// };

//Initial Theme check
// const themeCheck = () => {
//     if(userTheme === "dark" || !userTheme){
//         document.documentElement.classList.add("dark");
//         moonIcon.classList.add("display-none");
//         return
//     } 
//     sunIcon.classList.add("display-none");
// }

// Manual Theme Switch

// const themeSwitch = () => {
//     if(document.documentElement.classList.contains("dark")){
//         document.documentElement.classList.remove('dark');
//         localStorage.setItem("theme","dark");
//         iconToggle;
//     };
// }

// Call theme switch on clicking buttons
// sunIcon.addEventListener("click",() => {
//     themeSwitch();
// });

// moonIcon.addEventListener("click", ()=>{
//     themeSwitch();
// });


// invoke theme check on initial load
//themeCheck();

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');