function renderList(){
  const ul=document.getElementById('section-list');
  ul.innerHTML='';
  window.layout.order.forEach(key=>{
    const sec=window.layout.sections[key];
    if(!sec) return;
    const li=document.createElement('li');
    li.dataset.key=key;
    li.innerHTML=`<span>${sec.type}</span><span class="drag-handle">☰</span>`;
    ul.appendChild(li);
  });
  Sortable.create(ul,{handle:'.drag-handle',animation:150,onEnd:updateOrder});
}
function updateOrder(evt){
  const keys=[...document.querySelectorAll('#section-list li')].map(li=>li.dataset.key);
  window.layout.order=keys;
}
function openAddModal(){
  document.getElementById('add-section-modal').classList.add('show');
  document.getElementById('section-search').value='';
  filterSections('');
}

function closeModal(){
  document.getElementById('add-section-modal').classList.remove('show');
}

function filterSections(q){
  document.querySelectorAll('#add-section-modal .section-card').forEach(card=>{
    const title=card.querySelector('h4').innerText.toLowerCase();
    card.style.display=title.includes(q.toLowerCase())?'':'none';
  });
}

function addSection(type){
  fetch(`/themes/default/sections/${type}.schema.json`).then(r=>r.json()).then(schema=>{
    const sec={type:type,settings:{}};
    (schema.settings||[]).forEach(f=>{sec.settings[f.id]=f.default||'';});
    const key=type+'_'+Date.now();
    window.layout.sections[key]=sec;
    window.layout.order.push(key);
    renderList();
    saveLayout();
  }).catch(()=>{
    const key=type+'_'+Date.now();
    window.layout.sections[key]={type:type,settings:{}};
    window.layout.order.push(key);
    renderList();
    saveLayout();
  });
  closeModal();
}
function saveLayout(){
  fetch('/backend/themes/save-layout.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({theme_id:window.themeId,layout_json:JSON.stringify(window.layout)})}).then(r=>r.json()).then(d=>{if(d.success){document.getElementById('preview-frame').contentWindow.location.reload();}});
}

document.getElementById('add-section-btn').addEventListener('click',openAddModal);
document.getElementById('section-search').addEventListener('input',e=>filterSections(e.target.value));
document.querySelector('#add-section-modal .close-modal').addEventListener('click',closeModal);
document.querySelectorAll('#add-section-modal .section-card').forEach(card=>{
  card.addEventListener('click',()=>addSection(card.dataset.type));
});
document.getElementById('save-layout').addEventListener('click',saveLayout);
renderList();