(()=>{"use strict";const e=window.wp.blocks,t=window.wp.i18n,i=window.wp.element,n=window.wp.editor;(0,e.registerBlockType)("task-plugin/signup-list",{title:(0,t.__)("Signup List","task-plugin"),icon:"list-view",category:"widgets",edit:function(e){return console.log("Rendering edit function"),(0,i.createElement)("div",null,(0,i.createElement)("p",null,"Hello from the editor!"),(0,i.createElement)(n.ServerSideRender,{block:"task-plugin/signup-list",attributes:e.attributes}))},save:function(){return null}})})();