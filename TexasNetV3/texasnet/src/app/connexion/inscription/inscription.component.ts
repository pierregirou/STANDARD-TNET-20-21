import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../../services/http-request.service';
import { NgForm } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-inscription',
  templateUrl: './inscription.component.html',
  styleUrls: ['./inscription.component.css']
})
export class InscriptionComponent implements OnInit {
  hide:boolean=true;

  constructor(private httpRequest:HttpRequest, private router:Router, private httpClient:HttpClient) { }

  ngOnInit() {
  }

  onSubmit(form:NgForm){
    const societe = form.value["societe"];
    const nom     = form.value["nom"];
    const prenom  = form.value["prenom"];
    const email  = form.value["email"];
    const password = form.value["password"];
    const adresse1 = form.value["adresse1"];
    const adresse2 = form.value["adresse2"];
    const cp      = form.value["cp"];
    const ville   = form.value["ville"];
    const telephone   = form.value["telephone"];
    this.httpClient.post(this.httpRequest.inscriptionClient,{
      "societe":societe,
      "nom":nom,
      "prenom":prenom,
      "email":email,
      "adresse1":adresse1,
      "adresse2":adresse2,
      "cp":cp,
      "ville":ville,
      "telephone":telephone,
      "password":password
    }).subscribe(data=>{
        confirm(data['message']);
        
      if(data['statut'] === "true") {
        this.router.navigate(['connexion'])
      }
    });

  }
}
