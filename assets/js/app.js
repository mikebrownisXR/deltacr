document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('[data-toggle="modal"]').forEach(function(el){
    el.addEventListener('click',function(e){
      e.preventDefault();document.querySelector('.modal').classList.add('show');
    });
  });
  document.querySelectorAll('.modal .close').forEach(function(el){
    el.addEventListener('click',function(){document.querySelector('.modal').classList.remove('show');});
  });
});
