import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { Taille } from '../models/taille.model';
import { HttpRequest } from '../services/http-request.service';
import { Subscription } from 'rxjs';
import { LangueService } from '../services/langue.service';

@Injectable()
export class DetailService{
    quantite:number=5;
    langueSelect:number=1;
    langueSelectSubscription:Subscription;
    constructor(private httpClient:HttpClient, private httpRequest:HttpRequest, private langueService:LangueService){
      this.langueSelect=this.langueService.langueSelect;
      this.langueSelectSubscription=this.langueService.langueSelectSubject.subscribe(langue=>{
        this.langueSelect=langue;
      })
    }

    ngOnInit() {

    }

    getDetail(refproduit:string, codecolori:string='0634'){
      return new Promise(
        (resolve,reject)=>{
              console.log(refproduit, codecolori)
                let tmpLangue = this.langueService.getLangue();
                this.httpClient.post(this.httpRequest.detailProduit,{
                    "refproduit":refproduit,
                    "login":sessionStorage.getItem("loginCompte"),
                    "codecolori":codecolori,
                    "langue":tmpLangue
                }).subscribe(data=>{
                    resolve(data);
                })
            }
            )
    }

    getMiniatureSelonColoris(refproduit:string, coloris:string,saison:string){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.MiniatureColoris,{
                    "refproduit":refproduit,
                    "codeColoris":coloris,
                    "codeSaison":saison
                }).subscribe(data=>{
                    resolve(data);
                })
            }
        )
    }

}
