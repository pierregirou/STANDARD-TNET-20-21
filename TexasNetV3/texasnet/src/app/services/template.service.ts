import { Injectable } from '@angular/core'
import { Subject } from 'rxjs'
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../services/http-request.service';
@Injectable()

export class TemplateService{
    /* Modifie le backgroundColor dans toute l'appli */
    backgroundColor:string;
    backgroundColorSubject=new Subject<string>();
    /* Modifie la couleur du menu dans toute l'appli sauf dans l'administration */
    menuColor:string;
    menuColorSubject=new Subject<string>();
    /* Modifie la couleur du footer dans toute l'appli */
    footerColor:string;
    footerColorSubject=new Subject<string>();
    /* Modifie la couleur du contenu dans toute l'appli */
    contenuColor:string;
    contenuColorSubject=new Subject<string>();
    /* Modifie la couleur du module total dans toute l'appli */
    totalColor:string;
    totalColorSubject=new Subject<string>();
    /* Modifie la couleur du module info dans toute l'appli */
    infoColor:string;
    infoColorSubject=new Subject<string>();

    constructor(private httpClient:HttpClient,private httpRequest:HttpRequest){
        this.getBackgroundColor();
    }

    /* Pour le background */
    getBackgroundColor(){
      if(typeof(this.backgroundColor)==='undefined') {
        this.httpClient.post(this.httpRequest.InfoTemplate,{
            "choix":"infoBG"
        }).subscribe(bg=>{
            this.backgroundColor=bg[0];
            this.emitBackGroundColor();
        })
      } else {
        this.emitBackGroundColor();
      }
    }

    emitBackGroundColor(){
        this.backgroundColorSubject.next(this.backgroundColor);
    }

    /* Pour le menu */
    getMenuColor(){
      if(typeof(this.menuColor)==='undefined') {
        this.httpClient.post(this.httpRequest.InfoTemplate,{
            "choix":"infoMenuColor"
        }).subscribe(menuColor=>{
            this.menuColor=menuColor[0];
            this.emitMenuColor();
        });
      } else {
        this.emitMenuColor();
      }
    }

    emitMenuColor(){
        this.menuColorSubject.next(this.menuColor);
    }

    /* Pour le footer */
    getFooterColor(){
      if(typeof(this.footerColor)==='undefined') {
        this.httpClient.post(this.httpRequest.InfoTemplate,{
            "choix":"infoFooterColor"
        }).subscribe(footerColor=>{
            this.footerColor=footerColor[0];
            this.emitFooterColor();
        })
      } else {
        this.emitFooterColor();
      }
    }

    emitFooterColor(){
        this.footerColorSubject.next(this.footerColor);
    }

    /* Pour le contenu */
    getContenuColor(){
      if(typeof(this.contenuColor)==='undefined') {
        this.httpClient.post(this.httpRequest.InfoTemplate,{
            "choix":"infoContenuColor"
        }).subscribe(contenuColor=>{
            this.contenuColor=contenuColor[0];
            this.emitContenuColor();
        })
      } else {
        this.emitContenuColor();
      }
    }

    emitContenuColor(){
        this.contenuColorSubject.next(this.contenuColor);
    }

    /* Pour le total */
    getTotalColor(){
      if(typeof(this.totalColor)==='undefined') {
        this.httpClient.post(this.httpRequest.InfoTemplate,{
            "choix":"infoTotalColor"
        }).subscribe(totalColor=>{
            this.totalColor=totalColor[0];
            this.emitTotalColor();
        })
      } else {
        this.emitTotalColor();
      }
    }

    emitTotalColor(){
        this.totalColorSubject.next(this.totalColor);
    }

    /* Pour l'info */
    getInfoColor(){
      if(typeof(this.infoColor)==='undefined') {
        this.httpClient.post(this.httpRequest.InfoTemplate,{
            "choix":"infoInfoColor"
        }).subscribe(infoColor=>{
            this.infoColor=infoColor[0];
            this.emitInfoColor();
        })
      } else {
        this.emitInfoColor();
      }
    }

    emitInfoColor(){
        this.infoColorSubject.next(this.infoColor);
    }
}
