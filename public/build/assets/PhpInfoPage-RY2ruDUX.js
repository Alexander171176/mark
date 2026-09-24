import{m as b,f as M,o as v,w,i as e,q as x,n as C,p as T,D as S,a0 as D,E,t as V,k as L,M as B,a1 as j}from"./vendor-CugiUZmc.js";import{A as O}from"./AdminLayout-BI9msJDE.js";import{T as A}from"./TitlePage-BjMuyKva.js";import{_ as F}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./vendor-axios-jn5v1YEJ.js";import"./vendor-lodash-DV11DI02.js";import"./LocaleSelectOption-WQi5gS-_.js";import"./ResponsiveNavLink-HlKQO4xX.js";import"./ThemeToggle-DtC1BAgS.js";import"./ScrollButtons-DuXZ8_QA.js";const q={class:"px-2 py-2 w-full max-w-12xl mx-auto"},K={class:"p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"},R={class:"flex flex-col xl:flex-row xl:items-start xl:justify-between gap-3 mb-4"},W={class:"w-full xl:w-auto"},$={class:"flex flex-col sm:flex-row sm:items-center gap-2"},U={class:"relative w-full sm:w-96"},X={key:0,class:"mt-1 text-xs text-slate-600 dark:text-slate-300"},Y={class:"font-semibold text-sky-700 dark:text-sky-300"},G={class:"overflow-x-auto border border-slate-300 rounded-md shadow p-4 bg-white text-gray-900 text-sm"},J=["innerHTML"],Q={__name:"PhpInfoPage",props:{phpinfo:{type:String,default:""}},setup(z){const k=z,r=b(""),c=b(0),h=b(null),N=new Date().toISOString().slice(0,19).replace(/:/g,"-"),y=()=>{const n=h.value;if(!n){c.value=0;return}n.querySelectorAll("mark.php-info-search-mark").forEach(o=>{const l=o.parentNode;l&&(l.replaceChild(document.createTextNode(o.textContent||""),o),l.normalize())}),c.value=0},P=async()=>{y();const n=r.value.trim().toLowerCase();if(!n)return;await j();const t=h.value;if(!t)return;const o=document.createTreeWalker(t,window.NodeFilter.SHOW_TEXT),l=[];let p=o.nextNode();for(;p;){const a=p.parentElement;a&&a.tagName!=="SCRIPT"&&a.tagName!=="STYLE"&&l.push(p),p=o.nextNode()}let _=0,f=null;l.forEach(a=>{const d=a.nodeValue||"",g=d.toLowerCase();if(!g.includes(n))return;const m=document.createDocumentFragment();let i=0,s=g.indexOf(n);for(;s!==-1;){s>i&&m.appendChild(document.createTextNode(d.slice(i,s)));const u=document.createElement("mark");u.className="php-info-search-mark",u.textContent=d.slice(s,s+n.length),m.appendChild(u),f||(f=u),_++,i=s+n.length,s=g.indexOf(n,i)}i<d.length&&m.appendChild(document.createTextNode(d.slice(i))),a.parentNode&&a.parentNode.replaceChild(m,a)}),c.value=_,f&&f.scrollIntoView({behavior:"smooth",block:"center"})},H=()=>{r.value="",y()},I=()=>{const n=document.createElement("div");n.innerHTML=`
        <div style="
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #111;
            background: #fff;
        ">
            <h1 style="
                font-size: 18px;
                margin-bottom: 12px;
            ">
                PHP Info
            </h1>

            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 10px;
                    table-layout: fixed;
                }

                th,
                td {
                    border: 1px solid #999;
                    padding: 4px 6px;
                    vertical-align: top;
                    word-break: break-word;
                    overflow-wrap: anywhere;
                }

                .e {
                    background: #f0f0f0;
                    font-weight: bold;
                    width: 35%;
                }

                .v {
                    background: #ffffff;
                    width: 65%;
                }

                tr {
                    page-break-inside: avoid;
                }
            </style>

            ${k.phpinfo||""}
        </div>
    `;const t={margin:.35,filename:`php-info_${N}.pdf`,image:{type:"jpeg",quality:.98},html2canvas:{scale:1,logging:!1,useCORS:!0,backgroundColor:"#ffffff"},jsPDF:{unit:"in",format:"a4",orientation:"portrait"},pagebreak:{mode:["css","legacy"],avoid:["tr"]}};B().set(t).from(n).save()};return(n,t)=>(v(),M(O,{title:"PHP Info"},{header:w(()=>[L(A,null,{default:w(()=>t[1]||(t[1]=[x(" PHP Info ")])),_:1,__:[1]})]),default:w(()=>[e("div",q,[e("div",K,[t[6]||(t[6]=e("div",{class:"mb-4 px-4 py-3 border border-orange-300 dark:border-orange-700 bg-orange-50 dark:bg-orange-900/20 rounded-md"},[e("div",{class:"flex items-start gap-3"},[e("div",{class:"shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-800 text-orange-700 dark:text-orange-200"},[e("svg",{class:"w-5 h-5 fill-current",viewBox:"0 0 24 24"},[e("path",{d:`M11 17h2v-6h-2v6zm1-15C6.48 2
                                       2 6.48 2 12s4.48 10
                                       10 10 10-4.48 10-10S17.52
                                       2 12 2zm0 18c-4.41
                                       0-8-3.59-8-8s3.59-8
                                       8-8 8 3.59 8 8-3.59
                                       8-8 8zM11 9h2V7h-2v2z`})])]),e("div",null,[e("div",{class:"text-sm font-semibold text-orange-800 dark:text-orange-200"}," Конфигурация PHP "),e("div",{class:"mt-0.5 text-xs text-orange-700 dark:text-orange-300"},[x(" Здесь отображается информация, полученная непосредственно через "),e("span",{class:"font-mono font-semibold"}," phpinfo() "),x(". Страница доступна только для просмотра. ")])])])],-1)),e("div",R,[e("button",{type:"button",class:"h-8 px-3 inline-flex items-center justify-center gap-1 bg-teal-600 text-white rounded-sm hover:bg-teal-700 transition",onClick:I},t[2]||(t[2]=[e("svg",{class:"h-4 w-4 fill-current",viewBox:"0 0 384 512"},[e("path",{d:`M181.9 256.1c-5-16-4.9-46.9-2-46.9
                   8.4 0 7.6 36.9 2 46.9zm-1.7
                   47.2c-7.7 20.2-17.3 43.3-28.4
                   62.7 18.3-7 39-17.2
                   62.9-21.9-12.7-9.6-24.9-23.4-34.5-40.8zM86.1
                   428.1c0 .8 13.2-5.4
                   34.9-40.2-6.7 6.3-29.1
                   24.5-34.9 40.2zM248
                   160h136v328c0 13.3-10.7
                   24-24 24H24c-13.3
                   0-24-10.7-24-24V24C0
                   10.7 10.7 0 24 0h200v136c0
                   13.2 10.8 24 24
                   24zm-8 171.8c-20-12.2-33.3-29-42.7-53.8
                   4.5-18.5 11.6-46.6
                   6.2-64.2-4.7-29.4-42.4-26.5-47.8-6.8-5
                   18.3-.4 44.1 8.1
                   77-11.6 27.6-28.7
                   64.6-40.8 85.8-.1 0-.1.1-.2.1-27.1
                   13.9-73.6 44.5-54.5
                   68 5.6 6.9 16 10
                   21.5 10 17.9 0 35.7-18
                   61.1-61.8 25.8-8.5 54.1-19.1
                   79-23.2 21.7 11.8
                   47.1 19.5 64 19.5 29.2
                   0 31.2-32 19.7-43.4-13.9-13.6-54.3-9.7-73.6-7.2zM377
                   105 279 7c-4.5-4.5-10.6-7-17-7h-6v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-74.1
                   255.3c4.1-2.7-2.5-11.9-42.8-9
                   37.1 15.8 42.8 9 42.8 9z`})],-1),e("span",null," PDF ",-1)])),e("div",W,[e("div",$,[e("div",U,[t[3]||(t[3]=e("svg",{class:"absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 fill-current text-slate-400",viewBox:"0 0 24 24"},[e("path",{d:`M9.5 3a6.5 6.5 0 1 0
                           3.98 11.64L19.85 21
                           21 19.85l-6.36-6.37A6.5
                           6.5 0 0 0 9.5 3zm0
                           2a4.5 4.5 0 1 1 0
                           9 4.5 4.5 0 0 1 0-9z`})],-1)),S(e("input",{"onUpdate:modelValue":t[0]||(t[0]=o=>r.value=o),type:"text",placeholder:"memory_limit, curl, PDO, openssl...",class:"w-full pl-8 pr-8 py-1.5 text-sm font-mono border border-slate-300 dark:border-slate-500 rounded-md bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500",onKeyup:D(P,["enter"])},null,544),[[E,r.value]]),r.value?(v(),C("button",{key:0,type:"button",title:"Очистить поиск",class:"absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition",onClick:H}," × ")):T("",!0)]),e("button",{type:"button",class:"h-8 px-3 inline-flex items-center justify-center bg-sky-600 text-white rounded-sm hover:bg-sky-700 transition",onClick:P}," Найти ")]),r.value?(v(),C("div",X,[t[4]||(t[4]=x(" Найдено: ")),e("span",Y,V(c.value),1)])):T("",!0)])]),e("div",G,[t[5]||(t[5]=e("h1",{class:"text-xl font-semibold mb-4 text-gray-900"}," PHP Info ",-1)),e("div",{ref_key:"phpInfoContent",ref:h,class:"php-info-content",innerHTML:k.phpinfo},null,8,J)]),t[7]||(t[7]=e("div",{class:"mt-3 text-xs text-slate-500 dark:text-slate-400"}," Информация PHP доступна только для просмотра. Изменение конфигурации PHP из административной панели не выполняется. ",-1))])])]),_:1}))}},de=F(Q,[["__scopeId","data-v-29961bd2"]]);export{de as default};
