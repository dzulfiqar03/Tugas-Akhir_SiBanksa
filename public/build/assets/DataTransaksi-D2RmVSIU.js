import{u as pe,g as o,a as T,b as d,h as xe,w as B,o as n,d as t,F as _,c as ge,l as me,f as g,i as w,n as C,j as H,t as c,L as be,T as fe,_ as ke,v as ye,q as A,e as he,B as z,C as M,r as N,y as we}from"./app-CuzoSVHP.js";import{_ as ve}from"./AuthenticatedLayout-BRnJE2hz.js";import{j as _e,v as E,b as Ce}from"./vfs_fonts-BO8qCJZr.js";import{_ as U}from"./InputLabel-CIY1_MvR.js";import{V as D,D as R}from"./datatables.net-vue3-B1KaMbQn.js";import"./responsive.dataTables-B9J4gbVu.js";import{_ as Te}from"./_plugin-vue_export-helper-DlAUqK2U.js";/* empty css            */import"./Preloader-BG1Y0xCu.js";import"./404-BPu-ozYD.js";import"./SessionExpired-DXDeGcvB.js";/* empty css                                                               */import"./_commonjs-dynamic-modules-TDtrdbi3.js";const De={key:0,class:"card w-full shadow-sm border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden"},Se={class:"flex flex-col gap-5 bg-gray-200 dark:bg-gray-800 transition-colors"},$e={key:0,class:"p-3"},Be={key:0,class:"w-full font-medium capitalize text-gray-700 dark:text-gray-300"},Ne={key:1,class:"w-full font-medium capitalize text-gray-700 dark:text-gray-300"},Ie={key:1,class:"grid gap-4"},Ae={class:"bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border border-gray-200 dark:border-gray-700"},Re={key:0,class:"bg-white accordion-wrapper overflow-hidden dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700"},je={class:"text-lg font-semibold mb-4 text-black dark:text-white"},ze={class:"flex flex-col"},Pe={class:"flex gap-2 mt-1"},Le={class:"text-[10px] text-gray-400 mt-1 italic"},Fe={key:0,class:"text-red-500 text-xs mt-1"},Me={class:"grid grid-cols-1 gap-4"},Ee={key:0,class:"flex flex-col"},Oe=["type","id","placeholder"],Ke={key:0,class:"text-xs text-emerald-600 mt-2 font-medium"},He={key:1,class:"mt-2 space-y-1"},Ue={class:"md:col-span-2 lg:col-span-3 flex justify-end items-center gap-3 pt-2"},Ve=["disabled"],Ge={class:"grid grid-cols-1 lg:grid-cols-7 gap-4"},We={class:"lg:col-span-5 bg-white dark:bg-gray-800 rounded-xl shadow p-5 overflow-hidden"},Je={class:"overflow-x-auto"},qe={class:"flex flex-col lg:flex-row lg:items-end justify-between mb-6"},Ye={class:"flex flex-wrap mb-5 lg:mb-0 items-center gap-2"},Ze={class:"flex flex-wrap md:flex-nowrap items-end justify-start gap-3"},Qe={class:"flex items-end gap-2"},Xe={class:"flex items-center gap-2"},et={class:"flex items-center gap-2"},tt={class:"hidden md:block"},at={class:"block md:hidden space-y-4"},rt={key:0,class:"text-[10px] text-gray-500 font-bold uppercase mb-2"},st={class:"flex justify-between items-start mb-3"},nt={class:"flex items-center gap-3"},ot={class:"w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-full flex items-center justify-center font-bold text-sm"},it={class:"font-bold text-gray-800 dark:text-white capitalize leading-tight"},lt={class:"text-[11px] text-gray-500 mt-0.5 flex items-center gap-2"},dt={key:0},ut={key:1,class:"italic text-gray-400"},ct=["onClick"],pt={class:"mt-2 space-y-1"},xt={key:0,class:"flex items-center gap-2"},gt={class:"flex flex-col"},mt={class:"text-[10px] font-bold text-gray-700 dark:text-gray-200"},bt={class:"text-[11px] font-mono text-emerald-600 dark:text-emerald-400"},ft={key:1,class:"flex items-center gap-2 py-1 px-2 bg-gray-50 dark:bg-gray-800/50 rounded-lg w-max"},kt={class:"text-[10px] text-gray-500"},yt={class:"grid grid-cols-1 gap-2 mt-4 pt-3 border-t border-gray-50 dark:border-gray-800"},ht=["onClick"],wt={key:1,class:"flex flex-col gap-2"},vt=["onClick"],_t={key:1,class:"flex flex-col items-center justify-center py-12 text-gray-400 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800"},Ct={key:2,class:"mt-6 flex flex-col items-center gap-4"},Tt={class:"text-xs text-gray-500 font-medium"},Dt={class:"text-gray-800 dark:text-white font-bold"},St={class:"text-gray-800 dark:text-white font-bold"},$t={class:"flex items-center gap-2 w-full"},Bt=["disabled"],Nt=["disabled"],It={class:"flex gap-1.5"},At={class:"lg:col-span-2 bg-gray-50 dark:bg-gray-700 rounded-xl shadow p-5"},Rt={class:"overflow-x-auto"},jt=["onClick"],zt={class:"py-2"},Pt={class:"border-gray-100 w-max dark:border-gray-800"},Lt={key:0,class:"profile-circle py-1 px-2 rounded-full border border-gray-600 text-gray-800 dark:text-white"},Ft={key:1,class:"profile-circle"},Mt={class:"py-2 font-medium text-black dark:text-white"},Et={__name:"DataTransaksi",props:{formdata:Object,items:Array,sidebardata:Object,document:Array,breadcrumbItems:Array,user:Object,transaction:Array,nasabah:Array,nasabahAll:Array,reporting:Array,countTransaction:Number,IDRW:Number,IDRT:Number},setup(j){D.use(R),D.use(R),D.use(R),D.use(R),D.use(R),window.JSZip=_e;const V=E.pdfMake?E.pdfMake.vfs:E.vfs;Ce.vfs=V;const p=j;console.log(p.nasabah);const k=N(!1),P=N(!1),l=pe({id:p.user.id,id_userdetail:p.user.user_detail.id,id_jadwal:"",fullName:"",pencatatan_setoran_id:"",bukti_pembayaran:"",metode_pencairan:"",fileDoc:[]}),G=r=>{z.get(route("show-nasabah",r))},W=M(()=>l.fileDoc.map((r,e)=>{const a=r.name.split(".").pop();return{original:r.name,dynamic:`Bukti_Pembayaran_${l.fullName.replace(" ","_")}_BankSampahRT0${p.IDRT}.${a}`,size:r.size}})),O=r=>{let e;if(typeof r=="string")try{e=JSON.parse(decodeURIComponent(escape(atob(r))))}catch(a){console.error("Gagal mendecode Base64:",a);return}else e=r;P.value=!0,l.id=e.user_detail?.id_user,l.fullName=e.user_detail?.fullName,l.id_userdetail=e.id_userdetail,l.id_jadwal=e.id_jadwal,l.pencatatan_setoran_id=e.pencatatan_items?.find(a=>a.pencatatan_setoran_id)?.pencatatan_setoran_id??null,l.metode_pencairan=e.user_detail?.pencairan_via||"Tunai",l.bukti_pembayaran="",k.value=!0,window.scrollTo({top:0,behavior:"smooth"})};window.uploadBukti=O;const K=r=>{const e=JSON.parse(decodeURIComponent(escape(atob(r))));Swal.fire({title:"Hapus data?",text:"Tindakan ini tidak bisa dibatalkan!",icon:"warning",showCancelButton:!0,confirmButtonColor:"#ef4444",confirmButtonText:"Ya, Hapus!"}).then(a=>{a.isConfirmed&&z.delete(route("bs.delete-transaction",e.id),{onSuccess:()=>Swal.fire("Dihapus!","Data berhasil dihapus.","success")})})};window.handleDelete=K;const J=r=>{const e=JSON.parse(decodeURIComponent(escape(atob(r))));Swal.fire({title:"Lakukan Pembukaan Transaksi?",text:"Bank sampah RT0"+p.IDRT+" akan dapat melakukan transaksi dan notifikasi mengenai pelaporan anda",icon:"question",showCancelButton:!0,confirmButtonColor:"#ef4444",confirmButtonText:"Ya, Kirim!"}).then(a=>{a.isConfirmed&&z.post(route("bs.chat-transaction",e.user_detail.id_user),{message:"Anda Belum mengisi rekening dan tidak bisa dicairkan, Isi dan lengkapi rekening terlebih dahulu!!"},{onSuccess:()=>{Swal.fire("Terkirim!","Pesan pengingat telah dikirim.","success"),window.location.reload()}})})};window.handleWA=J;const y=we(),q=M(()=>y.props.auth.user),v=N(null),I=N([]),h=N({page:0,pages:0,start:0,end:0,recordsDisplay:0}),Y=r=>{r&&(I.value=r.rows({search:"applied",page:"current"}).data().toArray(),h.value=r.page.info())},Z=M(()=>(p.nasabah.some(r=>r.user_detail?.pencairan_via==="Tunai"),{pageLength:5,responsive:!0,lengthMenu:[5,10,25,50],drawCallback:function(){const r=this.api();Y(r)},columns:[{data:"jadwal",render:(r,e,a,s)=>{const i=new Date(a.jadwal.tanggal_setoran),u={day:"2-digit",month:"short",year:"numeric"};return i.toLocaleDateString("id-ID",u)}},{data:"user_detail.fullName",render:(r,e,a)=>`<div class="capitalize">${a.user_detail.fullName||"-"}</div>`,defaultContent:"-"},{data:"user_bank",render:(r,e,a)=>{if(!a.user_bank||a.user_bank.length===0)return'<span class="text-gray-400">-</span>';const s=a.user_bank[0].nomor_rekening,i=s.length>7?s.slice(0,4)+" •••• "+s.slice(-3):"••••"+s.slice(-3);return`
        <div class="flex items-center gap-2 font-mono">
            <span id="rek-${a.id}" data-full="${s}" data-mask="${i}" class="text-xs">${i}</span>
            <button type="button"
                onclick="
                    const span = document.getElementById('rek-${a.id}');
                    const isMasked = span.innerText.includes('•');
                    if (isMasked) {
                        span.innerText = span.getAttribute('data-full');
                        this.innerHTML = '<i class=\\'fas fa-eye-slash text-[10px]\\'></i>';
                    } else {
                        span.innerText = span.getAttribute('data-mask');
                        this.innerHTML = '<i class=\\'fas fa-eye text-[10px]\\'></i>';
                    }
                "
                class="text-gray-400 hover:text-emerald-500 p-1">
                <i class="fas fa-eye text-[10px]"></i>
            </button>
        </div>
    `}},{data:"user_bank",render:(r,e,a)=>a.user_detail.userbank?a.user_bank[0].bank.short_name:"-"},{data:"pencatatan_items",render:(r,e,a)=>{const s=r.reduce((u,x)=>u+parseFloat(x.subtotal),0);return`<div class="font-bold text-blue-600">${new Intl.NumberFormat("id-ID",{style:"currency",currency:"IDR",minimumFractionDigits:0}).format(s)}</div>`},defaultContent:"Rp 0"},{data:"user_detail.pencairan_via",title:"Pencairan Via",render:(r,e,a)=>r==="Tunai"?`<span class="px-2 py-1 rounded-full text-[10px] bg-green-100 text-green-700">${r}</span>`:`<span class="px-2 py-1 rounded-full text-[10px] bg-red-100 text-red-700">${r}</span>`,className:"text-center"},{data:null,orderable:!1,className:"no-print text-center",render:(r,e,a)=>{const s=btoa(unescape(encodeURIComponent(JSON.stringify(a))));return a.user_transaction.length===0?!a.user_bank||a.user_bank.length===0?` <button
                                            onclick="window.uploadBukti('${s}')"
                                            class="flex items-center gap-2 px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-bell"></i> Kirim Bukti Pembayaran
                                        </button>`:` <button
                                            onclick="window.uploadBukti('${s}')"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-bell"></i> Kirim Bukti Pembayaran
                                        </button>`:`

                                        <div class="flex space-x-3">
                                            <button
                                            onclick=""
                                            class="flex items-center gap-2 px-3 py-1.5 bg-blue-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-check"></i> Transaksi Telah Dilakukan
                                        </button>

                                        <button
                                            onclick="window.handleDelete('${s}')"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20">
                                            <i class="fas fa-trash"></i> Hapus Transaksi
                                        </button>
                                            </div>`}}],layout:{topStart:null,topEnd:null,bottomStart:"info",bottomEnd:"paging"},buttons:[{extend:"pdfHtml5",text:'<i class="fa-solid fa-file-pdf mr-2"></i> PDF',className:"export-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm",pageSize:"A4",title:"Laporan Transaksi SiBanksa RT-0"+(y.props.auth.user.user_detail?.id_rt||"-")+" "+new Date().toLocaleDateString("id-ID").replace(/\//g,"-"),exportOptions:{columns:[0,1,2,3,4,5],format:{body:function(r,e,a,s){return a===2?s.querySelector("span")?s.querySelector("span").getAttribute("data-full"):r:typeof r=="string"?r.replace(/<[^>]*>?/gm,"").trim():r}}},customize:function(r){const e=p.nasabah?.reduce((x,m)=>{const b=m.pencatatan_items?.reduce((f,F)=>f+(parseFloat(F.subtotal)||0),0)||0;return x+b},0)||0,a=new Intl.NumberFormat("id-ID",{style:"currency",currency:"IDR",minimumFractionDigits:0}).format(e),s=r.content.find(x=>x.table);if(s){s.table.body.length;const x=s.table.body[0].length;s.table.body.forEach(b=>{for(let f=0;f<x;f++)(typeof b[f]>"u"||b[f]===null)&&(b[f]={text:""})}),x===6&&(s.table.widths=[80,"*",100,60,80,50]);let m=[];m.push({text:"TOTAL SETORAN BELUM CAIR",colSpan:x-2,alignment:"right",bold:!0,fillColor:"#f9fafb"});for(let b=0;b<x-3;b++)m.push({});m.push({text:a,bold:!0,color:"#10b981",alignment:"right",fillColor:"#f0fdf4"}),m.push({text:"",fillColor:"#f9fafb"}),s.table.body.push(m),s.layout="lightHorizontalLines"}const i=y.props.auth.user?.user_detail,u=i?.id_rt||"-";r.content.splice(0,1,{columns:[{stack:[{text:"SiBanksa",fontSize:22,bold:!0,color:"#10b981"},{text:"Sistem Informasi Bank Sampah Digital",fontSize:8,color:"#6b7280"}]},{stack:[{text:"LAPORAN TRANSAKSI",fontSize:16,bold:!0,alignment:"right"},{text:`UNIT RT-0${u}`,fontSize:10,alignment:"right",color:"#9ca3af"}],width:"*"}]},{canvas:[{type:"line",x1:0,y1:5,x2:515,y2:5,lineWidth:1,lineColor:"#10b981"}],margin:[0,5,0,15]}),r.content.push({text:`

`},{columns:[{text:"",width:"*"},{width:180,stack:[{text:`Gresik, ${new Date().toLocaleDateString("id-ID",{day:"numeric",month:"long",year:"numeric"})}`,alignment:"center"},{text:"Verifikator Lapangan",alignment:"center",margin:[0,5,0,40]},{text:`( ${i?.fullName||".........................."} )`,alignment:"center",bold:!0},{text:"ID: SBK-RT0"+u,alignment:"center",fontSize:8,color:"#9ca3af"}]}]}),r.styles.tableHeader={fillColor:"#10b981",color:"white",bold:!0,alignment:"center"}}},{extend:"excelHtml5",text:'<i class="fa-solid fa-file-excel mr-2"></i> Excel',className:"export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm",title:"Dokumen Transaksi SiBanksa RT-0"+(y.props.auth.user.user_detail?.id_rt||"-")+" "+new Date().toLocaleDateString("id-ID").replace(/\//g,"-"),exportOptions:{columns:":not(.no-print)"},customize:function(r){var e=r.xl.worksheets["sheet1.xml"];const a=p.nasabah?.reduce((m,b)=>{const f=b.pencatatan_items?.reduce((F,ce)=>F+(parseFloat(ce.subtotal)||0),0)||0;return m+f},0);$("row c",e).attr("s","25"),$("row:first c",e).attr("s","51");var s=[{id:"A",width:20},{id:"B",width:30},{id:"C",width:20},{id:"D",width:15},{id:"E",width:20},{id:"F",width:15}];s.forEach((m,b)=>{$(`col[min="${b+1}"]`,e).attr("width",m.width)});var i=$("row",e).length,u=i+1,x=`
        <row r="${u}" customHeight="1" ht="30">
            <c r="A${u}" t="inlineStr" s="51">
                <is><t>TOTAL KESELURUHAN SETORAN</t></is>
            </c>
            <c r="B${u}" s="51"></c>
            <c r="C${u}" s="51"></c>
            <c r="D${u}" s="51"></c>
            <c r="E${u}" t="n" s="67">
                <v>${a}</v>
            </c>
            <c r="F${u}" s="51"></c>
        </row>
    `;$("sheetData",e).append(x),$("mergeCells",e).length||$("worksheet",e).prepend('<mergeCells count="1"/>'),$("mergeCells",e).append(`<mergeCell ref="A${u}:D${u}"/>`)}},{extend:"print",text:'<i class="fa-solid fa-print mr-2"></i> Print',className:"export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm",title:"",customize:function(r){const e=p.nasabah?.reduce((i,u)=>{const x=u.pencatatan_items?.reduce((m,b)=>m+(parseFloat(b.subtotal)||0),0)||0;return i+x},0),a=new Intl.NumberFormat("id-ID",{style:"currency",currency:"IDR",minimumFractionDigits:0}).format(e),s=p.nasabah?.map((i,u)=>{const x=i.pencatatan_items?.reduce((b,f)=>b+(parseFloat(f.subtotal)||0),0)||0,m=new Intl.NumberFormat("id-ID",{style:"currency",currency:"IDR",minimumFractionDigits:0}).format(x);return`
            <tr style="border-bottom: 1px solid #f3f4f6;">
                <td style="padding: 12px; text-align: center;">${i.jadwal.tanggal_setoran}</td>
                <td style="padding: 12px; font-weight: 600; text-transform: uppercase;">
                    ${i.user_detail.fullName}
                </td>
                <td style="padding: 12px; text-align: center; font-family: monospace;">
                    ${i.user_bank[0]?.nomor_rekening||"-"}
                </td>
                <td style="padding: 12px;">
                    ${i.user_bank[0]?.bank?.short_name||"-"}
                </td>
                <td style="padding: 12px; text-align: right; font-weight: bold; color: #059669;">
                    ${m}
                </td>
                <td style="padding: 12px; text-align: center;">
                    <span style="padding: 2px 8px; border-radius: 10px; font-size: 10px; ${i.user_transaction?.length>0?"background:#dcfce7;color:#166534;":"background:#fee2e2;color:#991b1b;"}">
                        ${i.user_transaction?.length>0?"Selesai":"Belum"}
                    </span>
                </td>
            </tr>
        `}).join("");$(r.document.body).css("font-family","Poppins, sans-serif").prepend(`
        <div style="padding: 40px; border-top: 10px solid #10b981; background: white;">
            <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.03] pointer-events-none">
                    <i class="fas fa-recycle text-[20rem]"></i>
                </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #f3f4f6; padding-bottom: 20px; margin-bottom: 30px;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div
                            class="w-16 h-16 bg-emerald-600 rounded-xl flex items-center justify-center text-white text-3xl shadow-lg">
                            <i class="fas fa-leaf"></i>
                        </div>
                    <div>
                        <h1 style="margin: 0; font-size: 24px; font-weight: 900; color: #1f2937;">SiBanksa</h1>
                        <p style="margin: 0; font-size: 10px; color: #6b7280; font-weight: bold; letter-spacing: 1px;">SISTEM INFORMASI BANK SAMPAH</p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <h2 style="margin: 0; font-size: 28px; color: #d1d5db; letter-spacing: 4px;">TRANSAKSI</h2>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; margin-bottom: 40px; font-size: 14px;">
                <div>
                    <p style="color: #9ca3af; font-weight: bold; font-size: 10px; margin-bottom: 5px;">DITERIMA DARI:</p>
                    <p style="font-weight: bold; font-size: 18px; margin: 0;">${y.props.auth.user.user_detail.fullName}</p>
                    <p style="color: #6b7280; margin: 0;">${y.props.auth.user.user_detail.roles.role} SiBanksa</p>
                    <p style="color: #6b7280; margin: 0;">RT: ${y.props.auth.user.user_detail?.id_rt||"-"} / RW: 01</p>
                </div>
                <div style="text-align: right;">
                    <p style="color: #9ca3af; font-weight: bold; font-size: 10px; margin-bottom: 5px;">Dicetak Pada:</p>
                    <p style="font-weight: bold; font-size: 18px; margin: 0;">${new Date().toLocaleDateString("id-ID")}</p>
                    <p style="color: #6b7280; margin: 0;">Lokasi: Unit Bank Sampah RT-0${y.props.auth.user.user_detail?.id_rt||"-"}</p>
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
                <thead>
                    <tr style="background: #f9fafb; color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: 1px;">
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Tanggal Setor</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Nama Lengkap</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: center;">Nomor Rekening</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Bank</th>
                         <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Total Saldo</th>
                        <th style="padding: 12px; border-bottom: 2px solid #f3f4f6; text-align: left;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${s}
                </tbody>

                <tfoot>
                    <tr style="background: #f9fafb; color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: 1px;">
                        <th colspan="4" style="padding: 12px; border-top: 2px solid #f3f4f6; text-align: right;">Setoran yang belum dicairkan: </th>
                        <th style="padding: 12px; border-top: 2px solid #f3f4f6; text-align: left; font-weight: bold;">${a}</th>
                        <th style="padding: 12px; border-top: 2px solid #f3f4f6;"></th>
                    </tr>
                </tfoot>
            </table>

           <div style="display: flex; justify-content: flex-end; margin-top: 40px;">
                            <div style="text-align: center; width: 220px;">
                                <p style="font-size: 11px; margin-bottom: 60px;">Gresik, ${new Date().toLocaleDateString("id-ID",{day:"numeric",month:"long",year:"numeric"})}<br><b>Verifikator</b></p>
                                <div style="border-bottom: 1px solid #d1d5db; width: 180px; margin: 0 auto 5px;"></div>
                                <p style="font-weight: bold; font-size: 12px; text-transform: uppercase;">( Ketua Bank Sampah RT-0${y.props.auth.user.user_detail?.id_rt||"-"} )</p>
                                <p style="font-size: 9px; color: #9ca3af;">ID: SBK-RT0${y.props.auth.user.user_detail?.id_rt||"-"}</p>
                            </div>
                        </div>
        </div>
    `),$(r.document.body).find("table").last().hide()}}],language:{info:"Menampilkan _START_ - _END_ dari _TOTAL_ data",paginate:{previous:"← Sebelumnya",next:"Berikutnya →"},emptyTable:`<div class="flex flex-col items-center  justify-center rounded-2xl shadow-inner">
  <div class="relative animate-pulse">
    <svg
  class="w-28 h-28 text-gray-400 dark:text-gray-500"
  viewBox="0 0 100 100"
  fill="none"
  xmlns="http://www.w3.org/2000/svg"
>
  <!-- Bingkai tabel -->
  <rect x="12" y="20" width="62" height="50" rx="6" stroke="currentColor" stroke-width="2" />

  <!-- Garis pemisah header -->
  <line x1="12" y1="34" x2="74" y2="34" stroke="currentColor" stroke-width="2" />

  <!-- Garis pemisah kolom -->
  <line x1="34" y1="20" x2="34" y2="70" stroke="currentColor" stroke-width="1.5" />
  <line x1="56" y1="20" x2="56" y2="70" stroke="currentColor" stroke-width="1.5" />

  <!-- Label header (judul kolom) -->
  <line x1="17" y1="27" x2="29" y2="27" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
  <line x1="39" y1="27" x2="51" y2="27" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
  <line x1="61" y1="27" x2="69" y2="27" stroke="currentColor" stroke-width="2" stroke-linecap="round" />

  <!-- Baris kosong (putus-putus, makin redup ke bawah) -->
  <line x1="17" y1="46" x2="29" y2="46" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.45" />
  <line x1="39" y1="46" x2="51" y2="46" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.45" />
  <line x1="61" y1="46" x2="69" y2="46" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.45" />

  <line x1="17" y1="58" x2="29" y2="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.3" />
  <line x1="39" y1="58" x2="51" y2="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.3" />
  <line x1="61" y1="58" x2="69" y2="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.3" />

  <!-- Kaca pembesar di pojok kanan bawah: menandakan "dicari, tidak ditemukan" -->
  <circle cx="78" cy="72" r="13" stroke="currentColor" stroke-width="2.5" />
  <line x1="87" y1="81" x2="95" y2="89" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" />
  <line x1="73" y1="72" x2="83" y2="72" stroke="currentColor" stroke-width="2" stroke-linecap="round" />

  <!-- Aksen dekoratif kecil, senada dengan ikon utama -->
  <circle cx="8" cy="10" r="2" stroke="currentColor" stroke-width="1.3" />
  <circle cx="91" cy="13" r="1.6" fill="currentColor" />
  <path d="M5 80 L9 80" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
</svg>
  </div>

  <h1 class="text-xl capitalize font-extrabold text-gray-800 dark:text-gray-100 tracking-tight mb-2">
    Maaf! Belum melakukan pencatatan baru.
  </h1>
  <p class="text-sm text-gray-600 dark:text-gray-300 max-w-lg mx-auto">
    Silakan lakukan pencatatan setoran pada <span class="text-emerald-500">Halaman Penyetoran Sampah</span> untuk dapat melakukan transaksi.
  </p>
</div>`}})),Q={pageLength:5,responsive:!0,lengthMenu:[5,10,25,50],layout:{topStart:null,topEnd:null,bottomStart:null,bottomEnd:"paging"},language:{info:"Menampilkan _START_ - _END_ dari _TOTAL_ data",paginate:{previous:"← Sebelumnya",next:"Berikutnya →"},emptyTable:`<div class="flex flex-col items-center  justify-center rounded-2xl shadow-inner">
  <div class="relative animate-pulse">
    <svg
  class="w-28 h-28 text-gray-400 dark:text-gray-500"
  viewBox="0 0 100 100"
  fill="none"
  xmlns="http://www.w3.org/2000/svg"
>
  <!-- Bingkai tabel -->
  <rect x="12" y="20" width="62" height="50" rx="6" stroke="currentColor" stroke-width="2" />

  <!-- Garis pemisah header -->
  <line x1="12" y1="34" x2="74" y2="34" stroke="currentColor" stroke-width="2" />

  <!-- Garis pemisah kolom -->
  <line x1="34" y1="20" x2="34" y2="70" stroke="currentColor" stroke-width="1.5" />
  <line x1="56" y1="20" x2="56" y2="70" stroke="currentColor" stroke-width="1.5" />

  <!-- Label header (judul kolom) -->
  <line x1="17" y1="27" x2="29" y2="27" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
  <line x1="39" y1="27" x2="51" y2="27" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
  <line x1="61" y1="27" x2="69" y2="27" stroke="currentColor" stroke-width="2" stroke-linecap="round" />

  <!-- Baris kosong (putus-putus, makin redup ke bawah) -->
  <line x1="17" y1="46" x2="29" y2="46" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.45" />
  <line x1="39" y1="46" x2="51" y2="46" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.45" />
  <line x1="61" y1="46" x2="69" y2="46" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.45" />

  <line x1="17" y1="58" x2="29" y2="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.3" />
  <line x1="39" y1="58" x2="51" y2="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.3" />
  <line x1="61" y1="58" x2="69" y2="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 3" stroke-linecap="round" opacity="0.3" />

  <!-- Kaca pembesar di pojok kanan bawah: menandakan "dicari, tidak ditemukan" -->
  <circle cx="78" cy="72" r="13" stroke="currentColor" stroke-width="2.5" />
  <line x1="87" y1="81" x2="95" y2="89" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" />
  <line x1="73" y1="72" x2="83" y2="72" stroke="currentColor" stroke-width="2" stroke-linecap="round" />

  <!-- Aksen dekoratif kecil, senada dengan ikon utama -->
  <circle cx="8" cy="10" r="2" stroke="currentColor" stroke-width="1.3" />
  <circle cx="91" cy="13" r="1.6" fill="currentColor" />
  <path d="M5 80 L9 80" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
</svg>
  </div>

  <h1 class="text-lg capitalize font-extrabold text-gray-800 dark:text-gray-100 tracking-tight mb-2">
    Maaf! Belum ada nasabah terdaftar.
  </h1>
  <p class="text-sm text-gray-600 dark:text-gray-300 max-w-lg mx-auto">
    Silakan lakukan pendataan nasabah pada <span class="text-emerald-500">Halaman Data Nasabah</span> untuk dapat melihat daftar nasabah.
  </p>
</div>`}},X=()=>{v.value?.dt&&v.value.dt.page("previous").draw("page")},ee=()=>{v.value?.dt&&v.value.dt.page("next").draw("page")},te=r=>{v.value.dt.search(r.target.value).draw()},ae=r=>{const e=r.target.value,a=e?`^${e}$`:"";v.value.dt.column(5).search(a,!0,!1).draw()},re=r=>{v.value.dt.page.len(parseInt(r.target.value)).draw()},L=r=>{v.value.dt.button(r).trigger()},se=[{label:"Dashboard",url:route("dashboard")},{label:"Transaksi",url:route("data-transaksi")}],ne=r=>{Swal.fire({title:"Kirim Pengingat?",text:"Ketua RW akan menerima notifikasi mengenai pelaporan anda",icon:"question",showCancelButton:!0,confirmButtonColor:"#ef4444",confirmButtonText:"Ya, Kirim!"}).then(e=>{e.isConfirmed&&z.post(route("laporsetoran.send-reminder",r),{message:`Bank Sampah RT0${p.IDRT} menyelesaikan pelaporan dan mengajukan pembukaan rekening pencairan setoran`},{onSuccess:()=>Swal.fire("Terkirim!","Pesan pengingat telah dikirim.","success")})})},oe=r=>{if(!r)return"??";const a=r.split(" "),s=a[0]?.substring(0,1)||"",i=a[1]?.substring(0,1)||"";return(s+i).toUpperCase()},ie=()=>{const r=route("bs.add-transaction");l["post"](r,{forceFormData:!0,onSuccess:()=>{Swal.fire("Berhasil!","Data transaksi telah diproses.","success"),k.value=!1,l.reset()},onError:function(a){if(a.status===422){const s=a.responseJSON.errors;let i="",u=0;Object.keys(s).forEach(x=>{s[x].forEach(m=>{i+=` <li class="text-[11px] text-red-600 dark:text-red-400 flex items-center gap-2">
                           <span class="w-1 h-1 bg-red-400 rounded-full"></span>
                           ${m}
                       </li>`,u++}),$(`[name="${x}"]`).addClass("border-red-500 ring-1 ring-red-500")}),$("#error-count").text(u),$("#error-list").html(i),$("#error-message").removeClass("hidden").fadeIn(),Swal.fire("Gagal!","Silakan periksa kembali inputan Anda.","error")}else Swal.fire("Error",a.responseJSON?.message||"Maaf, Inputan Anda ada yang salah, silahkan cek kembali","error")}})},le=r=>{if(!r||r.length===0)return"Rp 0";const e=r.reduce((a,s)=>a+parseFloat(s.subtotal||0),0);return new Intl.NumberFormat("id-ID",{style:"currency",currency:"IDR",minimumFractionDigits:0}).format(e)},S=N([]),de=r=>{S.value.includes(r)?S.value=S.value.filter(e=>e!==r):S.value.push(r)},ue=r=>r?r.slice(0,4)+"••••"+r.slice(-2):"Belum diisi";return(r,e)=>(n(),o(_,null,[T(d(xe),{title:"Data Transaksi"}),T(ve,{sidebardata:j.sidebardata,"breadcrumb-items":se},{default:B(()=>[q.value.user_detail.status_transaction==="Belum Disetujui"?(n(),o("div",De,[t("div",Se,[p.transaction.length===0?(n(),o("div",$e,[...e[9]||(e[9]=[t("h3",{class:"border-b capitalize border-gray-400 dark:border-gray-600 font-bold text-xl py-5 text-red-600 dark:text-red-400 w-full"}," Anda belum melakukan pencatatan setoran nasabah !!! ",-1),t("span",{class:"w-full font-medium capitalize text-gray-700 dark:text-gray-300"}," Lakukan pencatatan pada menu manajemen nasabah -> Pencatatan Setoran ",-1)])])):p.reporting.length>0?(n(),o(_,{key:1},[e[10]||(e[10]=t("h3",{class:"border-b capitalize border-gray-400 dark:border-gray-600 font-bold text-xl py-5 text-red-600 dark:text-red-400 w-full"}," Anda belum melakukan pelaporan setoran ke RW !!! ",-1)),p.reporting.length>0?(n(),o("span",Be," Lakukan pengajuan pelaporan ke RW dengan menekan tombol reminder dibawah ini ")):(n(),o("span",Ne," Lakukan pelaporan dengan upload dokumen hasil setoran atau foto bukti pelaksanaan kegiatan melalui menu manajemen nasabah -> Pelaporan setoran "))],64)):(n(),o(_,{key:2},[e[11]||(e[11]=t("h3",{class:"border-b border-gray-400 dark:border-gray-600 font-bold text-xl py-5 text-red-600 dark:text-red-400 w-full"}," Anda belum melakukan verifikasi akun !!! ",-1)),e[12]||(e[12]=t("span",{class:"w-full font-medium text-gray-700 dark:text-gray-300"}," Isi Biodata anda dan keperluan dokumen (Opsional) ",-1))],64)),p.transaction.length===0?(n(),ge(d(me),{key:3,href:"/bank-sampah/pencatatan",class:"flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20"},{default:B(()=>[...e[13]||(e[13]=[t("i",{class:"fas fa-bell"},null,-1),g(" Anda Belum Melakukan Pencatatan Setoran ",-1)])]),_:1})):p.reporting.length>0?(n(),o("button",{key:4,onClick:e[0]||(e[0]=a=>ne(p.IDRW)),class:"flex items-center gap-2 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-lg transition shadow-md shadow-red-500/20"},[...e[14]||(e[14]=[t("i",{class:"fas fa-bell"},null,-1),g(" Lakukan Pengajuan Persetujuan Buka Rekening Ke RW ",-1)])])):w("",!0)])])):(n(),o("div",Ie,[t("div",Ae,[t("div",{class:C(["flex flex-wrap justify-between items-center gap-4",[k.value?"mb-4":"mb-0"]])},[e[15]||(e[15]=t("h3",{class:"text-lg font-bold text-black dark:text-white"},"Pencairan Dana Nasabah",-1)),H(t("button",{onClick:e[1]||(e[1]=a=>k.value=!k.value),class:C(["text-white px-5 py-2.5 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-500/20 active:scale-95",[k.value?"bg-red-500 hover:bg-red-600 shadow-red-500/20":"bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20"]])},[t("i",{class:C(["fas",k.value?"fa-times":"fa-plus"])},null,2),g(" "+c(k.value?"Tutup Form":"Tambah Transaksi"),1)],2),[[be,k.value]])],2),T(fe,{name:"accordion"},{default:B(()=>[k.value?(n(),o("div",Re,[t("h3",je,c(P.value?"Perbarui Data":"Input Data Baru"),1),T(ke,{errors:d(l).errors,processing:d(l).processing,onSubmit:ie},{default:B(()=>[H(t("input",{type:"hidden",name:"id_userdetail","onUpdate:modelValue":e[2]||(e[2]=a=>d(l).id_userdetail=a)},null,512),[[ye,d(l).id_userdetail]]),t("div",ze,[T(U,{for:"metode_pencairan",value:"Metode Pencairan (untuk transaksi ini)"}),t("div",Pe,[t("button",{type:"button",onClick:e[3]||(e[3]=a=>d(l).metode_pencairan="Tunai"),class:C([d(l).metode_pencairan==="Tunai"?"bg-emerald-500 text-white border-emerald-500":"bg-white dark:bg-gray-900 text-gray-500 border-gray-200 dark:border-gray-700","flex-1 px-4 py-2.5 rounded-xl text-sm font-bold border transition"])},[...e[16]||(e[16]=[t("i",{class:"fas fa-money-bill-wave mr-1"},null,-1),g(" Tunai ",-1)])],2),t("button",{type:"button",onClick:e[4]||(e[4]=a=>d(l).metode_pencairan="Non-Tunai"),class:C([d(l).metode_pencairan==="Non-Tunai"?"bg-blue-500 text-white border-blue-500":"bg-white dark:bg-gray-900 text-gray-500 border-gray-200 dark:border-gray-700","flex-1 px-4 py-2.5 rounded-xl text-sm font-bold border transition"])},[...e[17]||(e[17]=[t("i",{class:"fas fa-university mr-1"},null,-1),g(" Non Tunai ",-1)])],2)]),t("p",Le," Preferensi nasabah: "+c(d(l).fullName&&p.nasabahAll?.find(a=>a.user_detail?.id_user===d(l).id)?.user_detail?.pencairan_via||"-")+". Pilihan di atas hanya berlaku untuk transaksi ini. ",1),d(l).errors.metode_pencairan?(n(),o("p",Fe,c(d(l).errors.metode_pencairan),1)):w("",!0)]),t("div",Me,[(n(!0),o(_,null,A(j.formdata.Dokumen,a=>(n(),o(_,{key:a.name},[a.type==="file"&&a.name==="fileDoc"?(n(),o("div",Ee,[T(U,{for:a.name,value:a.title},null,8,["for","value"]),t("input",{type:a.type,id:a.name,onInput:e[5]||(e[5]=s=>{const i=Array.from(s.target.files);d(l).fileDoc=[...d(l).fileDoc,...i]}),placeholder:a.placeholder,class:C(["w-full h-11 rounded-xl text-black bg-gray-50 dark:bg-gray-800 dark:text-white pl-5 text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm border-gray-200",{"border-red-500 ring-1 ring-red-500":d(l).errors[a.name]}])},null,42,Oe),d(l)[a.name]?.length?(n(),o("p",Ke,c(d(l)[a.name].length)+" file terpilih ",1)):w("",!0),d(l).fileDoc.length>0?(n(),o("ul",He,[(n(!0),o(_,null,A(W.value,(s,i)=>(n(),o("li",{key:i,class:"text-xs text-gray-500 flex items-center"},[e[18]||(e[18]=t("svg",{class:"w-3 h-3 mr-1 text-emerald-500",fill:"currentColor",viewBox:"0 0 20 20"},[t("path",{d:"M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"})],-1)),g(" "+c(s.dynamic)+" ("+c((s.size/1024).toFixed(1))+" KB) ",1)]))),128))])):w("",!0)])):w("",!0)],64))),128))]),t("div",Ue,[t("button",{type:"submit",class:"bg-emerald-500 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-emerald-600 transition disabled:opacity-50",disabled:d(l).processing},[e[19]||(e[19]=t("i",{class:"fas fa-save mr-2"},null,-1)),g(" "+c(P.value?"Upload Bukti Pembayaran":"Simpan Dokumen"),1)],8,Ve)])]),_:1},8,["errors","processing"])])):w("",!0)]),_:1})]),t("div",Ge,[t("div",We,[e[41]||(e[41]=t("h3",{class:"mb-4 font-bold text-gray-500 dark:text-white text-sm uppercase tracking-wider"}," Riwayat Transaksi",-1)),t("div",Je,[t("div",qe,[t("div",Ye,[t("button",{onClick:e[6]||(e[6]=a=>L(0)),class:"flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm"},[...e[20]||(e[20]=[t("i",{class:"fas fa-file-pdf"},null,-1),g(" PDF ",-1)])]),t("button",{onClick:e[7]||(e[7]=a=>L(1)),class:"flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm"},[...e[21]||(e[21]=[t("i",{class:"fas fa-file-excel"},null,-1),g(" Excel ",-1)])]),t("button",{onClick:e[8]||(e[8]=a=>L(2)),class:"flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm"},[...e[22]||(e[22]=[t("i",{class:"fas fa-print"},null,-1),g(" Print ",-1)])])]),t("div",Ze,[t("div",Qe,[e[23]||(e[23]=t("label",{class:"text-xs m-auto font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"},"Cari:",-1)),t("input",{onKeyup:te,type:"text",class:"border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none w-30 transition-all",placeholder:"Ketik..."},null,32)]),t("div",Xe,[e[25]||(e[25]=t("label",{class:"text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"},"Kategori:",-1)),t("select",{onChange:ae,class:"border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 text-black dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 outline-none cursor-pointer"},[...e[24]||(e[24]=[t("option",{value:""},"Semua",-1),t("option",{value:"Tunai"},"Tunai",-1),t("option",{value:"Non-Tunai"},"Non Tunai",-1)])],32)]),t("div",et,[e[27]||(e[27]=t("label",{class:"text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"},"Show:",-1)),t("select",{onChange:re,class:"bg-transparent text-sm font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer"},[...e[26]||(e[26]=[t("option",{value:"5",selected:""},"5",-1),t("option",{value:"10"},"10",-1),t("option",{value:"25"},"25",-1)])],32)])])]),t("div",tt,[T(d(D),{ref_key:"dtInstance",ref:v,data:j.nasabah,options:Z.value,class:"w-full stripe hover"},{default:B(()=>[...e[28]||(e[28]=[t("thead",null,[t("tr",{class:"text-gray-500"},[t("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Tanggal Setor"),t("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Nasabah"),t("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Nomor Rekening"),t("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Bank"),t("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Total Saldo"),t("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Status"),t("th",{class:"pb-4 font-semibold uppercase text-[12px] tracking-wider text-center"},"Aksi")])],-1)])]),_:1},8,["data","options"])]),t("div",at,[I.value.length>0?(n(),o("div",rt," Menampilkan "+c(I.value.length)+" Data Terfilter ",1)):w("",!0),(n(!0),o(_,null,A(I.value,(a,s)=>(n(),o("div",{key:a.id,class:"bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all active:scale-[0.98]"},[t("div",st,[t("div",nt,[t("div",ot,c(s+1),1),t("div",null,[t("h4",it,c(a.user_detail.fullName),1),t("p",lt,[e[29]||(e[29]=t("i",{class:"fas fa-phone"},null,-1)),a.user_detail.telephone_number?(n(),o("span",dt,c(S.value.includes(a.id)?a.user_detail.telephone_number:ue(a.user_detail.telephone_number)),1)):(n(),o("span",ut,"Belum diisi")),a.user_detail.telephone_number?(n(),o("button",{key:2,onClick:he(i=>de(a.id),["stop"]),class:"ml-1 text-gray-400 hover:text-emerald-500 transition-colors focus:outline-none"},[t("i",{class:C([S.value.includes(a.id)?"fas fa-eye-slash":"fas fa-eye","text-[10px]"])},null,2)],8,ct)):w("",!0)]),t("div",pt,[a.user_detail?.pencairan_via!=="Tunai"?(n(),o("div",xt,[t("div",gt,[t("span",mt,[e[30]||(e[30]=t("i",{class:"fas fa-university mr-1 opacity-50"},null,-1)),g(" "+c(a.user_bank?.[0]?.bank?.short_name||"-"),1)]),t("span",bt,c(a.user_bank?.[0]?.nomor_rekening||"-"),1)])])):(n(),o("div",ft,[...e[31]||(e[31]=[t("i",{class:"fas fa-hand-holding-usd text-gray-400 text-[10px]"},null,-1),t("span",{class:"text-[10px] italic text-gray-500"},"Penerimaan Tunai",-1)])]))]),t("h1",null,[t("span",kt,"Total: "+c(le(a.pencatatan_items)),1)])])]),t("span",{class:C(["px-2 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider",a.user_detail.pencairan_via==="Tunai"?"bg-emerald-500 text-white":"bg-amber-600 text-white"])},c(a.user_detail.pencairan_via),3)]),t("div",yt,[a.user_transaction.length===0?(n(),o("button",{key:0,onClick:i=>O(a),class:"flex items-center justify-center gap-2 px-3 py-2.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-xl transition shadow-md shadow-red-500/20"},[...e[32]||(e[32]=[t("i",{class:"fas fa-bell"},null,-1),g(" Kirim Bukti Pembayaran ",-1)])],8,ht)):(n(),o("div",wt,[e[34]||(e[34]=t("button",{disabled:"",class:"flex items-center justify-center gap-2 px-3 py-2.5 bg-blue-500 text-white text-[11px] font-bold rounded-xl shadow-md shadow-blue-500/20"},[t("i",{class:"fas fa-check"}),g(" Transaksi Telah Dilakukan ")],-1)),t("button",{onClick:i=>K(a.id),class:"flex items-center justify-center gap-2 px-3 py-2.5 bg-red-500 hover:bg-red-600 text-white text-[11px] font-bold rounded-xl transition shadow-md shadow-red-500/20"},[...e[33]||(e[33]=[t("i",{class:"fas fa-trash"},null,-1),g(" Hapus Transaksi ",-1)])],8,vt)]))])]))),128)),I.value.length===0?(n(),o("div",_t,[...e[35]||(e[35]=[t("i",{class:"fas fa-search text-4xl mb-3 opacity-20"},null,-1),t("p",{class:"text-sm font-medium"},"Data tidak ditemukan",-1),t("p",{class:"text-[10px]"},"Coba gunakan kata kunci pencarian lain",-1)])])):w("",!0),h.value.recordsDisplay>0?(n(),o("div",Ct,[t("span",Tt,[e[36]||(e[36]=g(" Menampilkan ",-1)),t("span",Dt,c(h.value.start+1)+"-"+c(h.value.end),1),e[37]||(e[37]=g(" dari ",-1)),t("span",St,c(h.value.recordsDisplay),1),e[38]||(e[38]=g(" data ",-1))]),t("div",$t,[t("button",{onClick:X,disabled:h.value.page===0,class:"flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 disabled:opacity-30 disabled:grayscale bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 active:scale-95"},[...e[39]||(e[39]=[t("i",{class:"fas fa-chevron-left text-[10px]"},null,-1),g(" Sebelumnya ",-1)])],8,Bt),t("button",{onClick:ee,disabled:h.value.page>=h.value.pages-1,class:"flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 disabled:opacity-30 disabled:grayscale bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 active:scale-95"},[...e[40]||(e[40]=[g(" Berikutnya ",-1),t("i",{class:"fas fa-chevron-right text-[10px]"},null,-1)])],8,Nt)]),t("div",It,[(n(!0),o(_,null,A(h.value.pages,a=>(n(),o("div",{key:a,class:C(["w-1.5 h-1.5 rounded-full transition-all duration-300",a===h.value.page+1?"bg-emerald-500 w-4":"bg-gray-300 dark:bg-gray-700"])},null,2))),128))])])):w("",!0)])])]),t("div",At,[e[45]||(e[45]=t("h3",{class:"mb-4 font-bold text-center border-b dark:border-gray-600 pb-2 dark:text-white text-black text-sm uppercase"}," Pilih Nasabah",-1)),t("div",Rt,[T(d(D),{options:Q,class:"w-full text-xs"},{default:B(()=>[e[44]||(e[44]=t("thead",null,[t("tr",{class:"text-left border-b dark:border-gray-600"},[t("th",{class:"pb-2 text-black dark:text-white"},"Profil"),t("th",{class:"pb-2 text-black dark:text-white"},"Nama"),t("th",{class:"pb-2 text-black dark:text-white"},"Aksi")])],-1)),t("tbody",null,[(n(!0),o(_,null,A(p.nasabahAll,a=>(n(),o("tr",{key:a.id,onClick:s=>G(a.user_detail.id_user),class:"cursor-pointer hover:bg-emerald-50 text-black dark:text-white dark:hover:bg-gray-600 transition border-b dark:border-gray-600 last:border-0"},[t("td",zt,[t("div",Pt,[a?(n(),o("div",Lt,c(oe(a.user_detail?.fullName)),1)):(n(),o("div",Ft,[...e[42]||(e[42]=[t("img",{class:"w-8 h-8 rounded-full",src:"https://ui-avatars.com/api/?name=Guest&background=random",alt:"Guest"},null,-1)])]))])]),t("td",Mt,c(a.user_detail.fullName),1),e[43]||(e[43]=t("td",{class:"py-2 text-right"},[t("i",{class:"fas fa-chevron-right text-gray-400"})],-1))],8,jt))),128))])]),_:1})])])])]))]),_:1},8,["sidebardata"])],64))}},ea=Te(Et,[["__scopeId","data-v-ea70072b"]]);export{ea as default};
