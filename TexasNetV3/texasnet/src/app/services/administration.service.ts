import { HttpClient } from '@angular/common/http';
import { Injectable } from "@angular/core";
import { HttpRequest } from './http-request.service';

@Injectable()
export class AdministrationService{
    constructor(private httpClient:HttpClient,private httpRequest:HttpRequest){}

    updateModule(module:string,valeur:string){
        this.httpClient.post(this.httpRequest.Administration,{
            "module":module,
            "valeur":valeur
        }).subscribe()
    }
}