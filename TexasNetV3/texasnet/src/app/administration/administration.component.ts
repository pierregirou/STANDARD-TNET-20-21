import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpRequest } from '../services/http-request.service';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-administration',
  templateUrl: './administration.component.html',
  styleUrls: ['./administration.component.css']
})
export class AdministrationComponent implements OnInit {
  nomSociete:string;
  adresse1:string;
  adresse2:string;
  email:string;
  fax:string;
  siteweb:string;
  telephone:string;
  codeDevise:string;
  codeLangue:string;
  codeTarif:string;
  dateMinLivraison:string;
  photoHauteur:string;
  photoLargeur:string;
  saisonCommande:string;
  texteCommandeAng:string;
  texteCommandeFra:string
  

  selection:string; //permet de connaitre la sélection sur le panel d'administration
  constructor(private httpClient:HttpClient,private httpRequest:HttpRequest,private router:Router, private route:ActivatedRoute) { }

  ngOnInit() {

    this.httpClient.post(this.httpRequest.InfoParametrages,{
      "parametrages":"ok"
    }).subscribe(data=>{
      this.nomSociete=data[1].nomSociete;
      this.adresse1=data[1].adresse1;
      this.adresse2=data[1].adresse2;
      this.email=data[1].email;
      this.fax=data[1].fax;
      this.siteweb=data[1].siteweb;
      this.telephone=data[1].telephone;
      this.codeDevise=data[1].codeDevise;
      this.codeLangue=data[1].codeLangue;
      this.codeTarif=data[1].codeTarif;
      this.dateMinLivraison=data[1].dateMinLivraison;
      this.photoHauteur=data[1].photoHauteur;
      this.photoLargeur=data[1].photoLargeur;
      this.saisonCommande=data[1].saisonCommande;
      this.texteCommandeAng=data[1].texteCommandeAng;
      this.texteCommandeFra=data[1].texteCommandeFra
    });

    //si l'utilisateur n'est pas un administrateur le redirige
    if(sessionStorage.getItem("admin")!=="true"){
      this.router.navigate(['']);
      sessionStorage.clear();
    }
    this.route.params.subscribe(
      (value)=>{
        this.selection=value.action;
      }
    )
  }

  changeValueParametrage(choix,value){
    this.httpClient.post(this.httpRequest.UpdateParametrages,{
      "choix":choix,
      "value":value
    }).subscribe();
  }
}
