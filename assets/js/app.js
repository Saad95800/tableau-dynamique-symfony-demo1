/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';

document.querySelector('#add-good').addEventListener('click', ()=>{
    document.querySelector('#black-screen').style.display = 'flex';
});

document.querySelector('#black-screen').addEventListener('click', ()=>{
    document.querySelector('#black-screen').style.display = 'none';
});

document.querySelector('#black-screen').querySelector('form').addEventListener('click', function(e){
    e.stopPropagation();
})