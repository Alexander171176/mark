import{A as d}from"./AdminLayout-Dsnq8JJY.js";import{T as l}from"./TitlePage-Da3OB2FB.js";import{f as p,o as f,w as a,i as t,k as c,q as m,M as g}from"./vendor-CMSFekJy.js";import{_ as h}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./LocaleSelectOption-C0bz216F.js";import"./ResponsiveNavLink-HK1a5h-B.js";import"./ThemeToggle-OKgSLPve.js";import"./ScrollButtons-CDD4DA8f.js";import"./vendor-axios-jn5v1YEJ.js";import"./vendor-lodash-CTulkke8.js";const b={class:"px-2 py-2 w-full max-w-12xl mx-auto"},u={class:"p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"},x={id:"phpInfoContent",class:"overflow-x-auto border rounded-md shadow p-4 bg-white text-gray-900 text-sm"},w=["innerHTML"],v={__name:"PhpInfoPage",props:{phpinfo:String},setup(r){const n=r,s=new Date().toISOString().slice(0,19).replace(/:/g,"-"),i=()=>{const o=document.createElement("div");o.innerHTML=`
        <div style="font-family: Arial, sans-serif; font-size: 9px; color: #111; background: #fff;">
            <h1 style="font-size: 18px; margin-bottom: 12px;">PHP Info</h1>

            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 10px;
                    table-layout: fixed;
                }

                th, td {
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

            ${n.phpinfo||""}
        </div>
    `;const e={margin:.35,filename:`php-info_${s}.pdf`,image:{type:"jpeg",quality:.98},html2canvas:{scale:1,logging:!1,useCORS:!0,backgroundColor:"#ffffff"},jsPDF:{unit:"in",format:"a4",orientation:"portrait"},pagebreak:{mode:["css","legacy"],avoid:["tr"]}};g().set(e).from(o).save()};return(o,e)=>(f(),p(d,{title:"PHP Info"},{header:a(()=>[c(l,null,{default:a(()=>e[0]||(e[0]=[m("PHP Info")])),_:1,__:[0]})]),default:a(()=>[t("div",b,[t("div",u,[t("div",{class:"flex justify-end mb-3"},[t("button",{type:"button",onClick:i,class:"h-8 px-3 inline-flex items-center justify-center gap-1 bg-teal-600 text-white rounded-sm hover:bg-teal-700"},e[1]||(e[1]=[t("svg",{class:"h-4 w-4 fill-current",viewBox:"0 0 384 512"},[t("path",{d:"M181.9 256.1c-5-16-4.9-46.9-2-46.9 8.4 0 7.6 36.9 2 46.9zm-1.7 47.2c-7.7 20.2-17.3 43.3-28.4 62.7 18.3-7 39-17.2 62.9-21.9-12.7-9.6-24.9-23.4-34.5-40.8zM86.1 428.1c0 .8 13.2-5.4 34.9-40.2-6.7 6.3-29.1 24.5-34.9 40.2zM248 160h136v328c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V24C0 10.7 10.7 0 24 0h200v136c0 13.2 10.8 24 24 24zm-8 171.8c-20-12.2-33.3-29-42.7-53.8 4.5-18.5 11.6-46.6 6.2-64.2-4.7-29.4-42.4-26.5-47.8-6.8-5 18.3-.4 44.1 8.1 77-11.6 27.6-28.7 64.6-40.8 85.8-.1 0-.1.1-.2.1-27.1 13.9-73.6 44.5-54.5 68 5.6 6.9 16 10 21.5 10 17.9 0 35.7-18 61.1-61.8 25.8-8.5 54.1-19.1 79-23.2 21.7 11.8 47.1 19.5 64 19.5 29.2 0 31.2-32 19.7-43.4-13.9-13.6-54.3-9.7-73.6-7.2zM377 105L279 7c-4.5-4.5-10.6-7-17-7h-6v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-74.1 255.3c4.1-2.7-2.5-11.9-42.8-9 37.1 15.8 42.8 9 42.8 9z"})],-1),t("span",null,"PDF",-1)]))]),t("div",x,[e[2]||(e[2]=t("h1",{class:"text-xl font-semibold mb-4"}," PHP Info ",-1)),t("div",{innerHTML:r.phpinfo,class:"php-info-content"},null,8,w)])])])]),_:1}))}},D=h(v,[["__scopeId","data-v-f7c0db98"]]);export{D as default};
