import { Injectable } from '@angular/core';
import { Subscription } from 'rxjs';
import { Subject } from 'rxjs';
import { HttpRequest } from '../services/http-request.service';
import { HttpClient } from '@angular/common/http';
import { ProduitService } from '../services/produits.service';
import { LangueService } from '../services/langue.service';
import { ImageService } from "../services/images.service";
import { Produits } from '../models/produits.model';


@Injectable({
  providedIn: 'root'
})
export class MenuService {
  private menu:any;
  sousMenu1:any[];
  sousMenu2:any[];
  sousMenu3:any[];
  sousMenu4:any[];
  sousMenu5:any[];
  public menuSubject = new Subject<any[]>();
  langueSelect:string='FRA';
  langueSelectSubscription:Subscription;

  constructor(private imageService: ImageService, private httpClient: HttpClient, private httpRequest: HttpRequest, private produitService: ProduitService,  private langueService:LangueService) { }

  ngOnInit() {}

  appelMenu() {
    console.log(this.menu)
    this.menuSubject.next(this.menu);
  }

  detecteLangueMenu() {
    this.langueSelectSubscription=this.langueService.langueSelectSubject.subscribe(langue=>{
      this.langueSelect = langue === 1 ? 'FRA':'ANG';
      this.initialiseMenu();
    });
  }

  initialiseMenu() {
    this.httpClient.post(this.httpRequest.InfoMenu, {
      "cryptKey": "eJhG487711G56D14532Ddgj",
      "login": sessionStorage.getItem("loginCompte"),
      "langue":this.langueSelect
    }).subscribe(data => {
      console.log('data '+ data)
      this.menu = data;
      this.appelMenu();
    });
  }
}
