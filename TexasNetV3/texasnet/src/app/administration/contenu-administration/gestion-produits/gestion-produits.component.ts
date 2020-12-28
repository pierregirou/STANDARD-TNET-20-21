import { Component, OnInit } from '@angular/core';
import { ModuleService } from '../../../services/modules.service';
import { HttpRequest } from '../../../services/http-request.service';
import { HttpClient } from '@angular/common/http';
import { ProduitService } from '../../../services/produits.service';
import {FormControl} from '@angular/forms';
import {Observable,Subscription} from 'rxjs';
import {map, startWith} from 'rxjs/operators';
import { ScrollToService, ScrollToConfigOptions } from '@nicky-lenaers/ngx-scroll-to';
import { MatSnackBar } from '@angular/material';
import { faSmile } from '@fortawesome/free-solid-svg-icons';
@Component({
  selector: 'app-gestion-produits',
  templateUrl: './gestion-produits.component.html',
  styleUrls: ['./gestion-produits.component.css']
})
export class GestionProduitsComponent implements OnInit {
  ordreAffichage:string;
  constructor(private snackBar:MatSnackBar,private scrollToService:ScrollToService,private httpRequest:HttpRequest,private moduleService:ModuleService,private httpClient:HttpClient,private produitService:ProduitService) { }
  myControl = new FormControl();
  filteredProduits: Observable<string[]>;
  produitsGestion:any[]=[];
  produitsGestionSubscription:Subscription;
  recherche:string;
  cocher:number;
  selectAll = false;
  ngOnInit() {
    this.produitsGestionSubscription=this.produitService.produitSubjectGestion.subscribe(produits=>{
      for(let i=0;i<Object.keys(produits).length;i++){
        this.produitsGestion[i]=produits[i].libelle;
      }
      this.filteredProduits = this.myControl.valueChanges
      .pipe(
        startWith(''),
        map(value => this._filter(value))
      );
    });
    this.produitService.emitProduitsGestion();
    this.moduleService.displayOrder().then(data=>{
      this.ordreAffichage=String(data['ordreAffichage']);
    });

  }

  /* Filtre autocompletion */
  private _filter(value: string): string[] {
    const filterValue = value.toLowerCase();

    return this.produitsGestion.filter(produit => produit.toLowerCase().includes(filterValue));
    /* ******************* */
  }

  getDisplayOrder(id:number){
    this.httpClient.post(this.httpRequest.UpdateModules,{
      "ordreAffichage":id
    }).subscribe(data=>{
      this.ordreAffichage=data['idOrder'];
    })
  }

  /* Méthode permettant de scroll vers le produit choisi en appuyant sur entrer dans l'input type text */
  searchChange(event){
    this.recherche = event.toUpperCase();
  }
}
