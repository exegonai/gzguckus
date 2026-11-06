(()=>{"use strict";const b="gg-tercih",n={harita:{ad:"Harita",sag:"Google Haritalar",metin:"Konumu sayfada gösterir. IP adresiniz ve tarayıcı bilgileriniz Google’a iletilir."}},r=Object.keys(n),c=()=>{try{const t=JSON.parse(localStorage.getItem(b));return!t||t.s!==1||(Date.now()-new Date(t.t).getTime())/864e5>180?null:t}catch{return null}},p=t=>{try{localStorage.setItem(b,JSON.stringify({s:1,t:new Date().toISOString(),...t}))}catch{}},v={harita:t=>{if(t.querySelector("iframe"))return;const e=document.createElement("iframe");e.src=t.dataset.kaynak,e.title=t.dataset.baslik,e.loading="lazy",e.referrerPolicy="strict-origin-when-cross-origin",e.allowFullscreen=!0,t.appendChild(e)}},h=t=>{document.querySelectorAll("[data-rizali]").forEach(e=>{const a=e.dataset.rizali;!t||!t[a]||(e.classList.add("is-izinli"),v[a](e))})},s=t=>{const e=c()||{};if(p(t),z(),r.some(a=>e[a]&&!t[a])){location.reload();return}h(t)},o=t=>r.reduce((e,a)=>(e[a]=t,e),{}),g=document.querySelector(".trc"),l=document.documentElement,z=()=>{l.classList.add("trc-var"),l.classList.remove("trc-acik")},S=()=>{l.classList.remove("trc-var"),l.classList.add("trc-acik")},i=document.createElement("dialog");i.className="trc-pencere",i.setAttribute("aria-labelledby","trc-baslik"),i.setAttribute("data-lenis-prevent",""),i.innerHTML=`
    <form method="dialog">
      <h2 id="trc-baslik">Çerez tercihleri</h2>
      <p>Seçiminizi istediğiniz zaman alt bilgideki “Çerez Tercihleri” bağlantısından değiştirebilirsiniz.</p>
      <div class="trc-satir">
        <div><h3>Zorunlu</h3><p>Tercihinizi hatırlamak için bu tarayıcıda 180 gün saklanan bir kayıt. Kimliğinizi belirtmez, sunucumuza gönderilmez.</p></div>
        <span class="trc-sabit">Her zaman açık</span>
      </div>
      <div class="trc-satir">
        <div><h3>Online randevu takvimi</h3><p>İletişim sayfasında izin beklemeden yüklenir. Doktor Takvimi çerez ve yerel depolama kullanır, analiz ve hata kaydı hizmetlerine bağlanır. Sağlayıcı: Doktor Takvimi.</p></div>
        <span class="trc-sabit">Her zaman açık</span>
      </div>
      ${r.map(t=>`
      <label class="trc-satir" for="trc-${t}">
        <div><h3>${n[t].ad}</h3><p>${n[t].metin} Sağlayıcı: ${n[t].sag}.</p></div>
        <input type="checkbox" id="trc-${t}" name="${t}">
      </label>`).join("")}
      <div class="trc__dugmeler">
        <button type="button" data-trc="kabul">Kabul et</button>
        <button type="button" data-trc="ret">Reddet</button>
        <button type="button" data-trc="kaydet">Seçimi kaydet</button>
      </div>
    </form>`;const y=()=>{const t=c()||{};r.forEach(e=>{i.querySelector(`#trc-${e}`).checked=!!t[e]}),i.open||i.showModal()},d=()=>{i.open&&i.close()},f=t=>{const e=t.target.closest("[data-trc]");if(!e)return;const a=e.dataset.trc;if(a==="kabul")d(),s(o(!0));else if(a==="ret")d(),s(o(!1));else if(a==="tercih")y();else if(a==="kaydet"){const u={};r.forEach(m=>{u[m]=i.querySelector(`#trc-${m}`).checked}),d(),s(u)}};g.addEventListener("click",f),i.addEventListener("click",f),document.body.append(i),document.addEventListener("click",t=>{if(t.target.closest("[data-tercih-ac]")){t.preventDefault(),y();return}const a=t.target.closest("[data-izin-ver]");if(a){const{s:u,t:m,...A}=c()||{};s({...o(!1),...A,[a.dataset.izinVer]:!0})}});const k=c();h(k),k||S()})();
