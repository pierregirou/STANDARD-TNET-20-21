import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { HttpRequest } from '../../services/http-request.service';
import { TelechargementService } from '../../services/telechargement.service';
import { Telechargement } from '../../models/telechargement.models';
import { HttpClient } from '@angular/common/http';
import { MatSnackBar } from '@angular/material/snack-bar';


@Component({
  selector: 'app-telechargement-administration',
  templateUrl: './telechargement-administration.component.html',
  styleUrls: ['./telechargement-administration.component.css']
})
export class TelechargementAdministrationComponent implements OnInit {

  uploadForm;
  fileToUpload:File = null;
  telechargement:Telechargement[]=[];

  constructor(private httpClient:HttpClient, private formBuilder: FormBuilder, private telechargementService: TelechargementService,  private httpRequest: HttpRequest, private snackBar:MatSnackBar) {
    this.uploadForm = this.formBuilder.group({
      intitule: ['', Validators.required],
      url: [''],
      relativeLink: ['']
    });


  }

  ngOnInit() {
    this.telechargementService.getTelechargementList().then(
      (data)=>{
        var type;
        this.telechargement = [];
        var keys = Object.keys(data);
        for(let i=0; i<keys.length;i++){          
          String(data[i].type) === '1' ? type = "URL" : type = "UPLOAD"
          this.telechargement.push(new Telechargement(data[i].id, data[i].intitule,type,data[i].lien));
        }
      }
    );
  }

  onFileInput (files:FileList) {
    this.fileToUpload = files.item(0);
    this.uploadForm.get('relativeLink').setValue(this.fileToUpload.name);
    this.checkLinks('relative');
  }

  onUpload() {
    let arrayParameters = [];
    arrayParameters["intitule"] = this.uploadForm.get('intitule').value.toLowerCase();
    if (this.uploadForm.get('relativeLink').value === '') {
      arrayParameters["type"]="1";
      arrayParameters["lien"]=this.uploadForm.get('url').value;
    } else {
      arrayParameters["type"]="2";
      arrayParameters["lien"]=this.uploadForm.get('relativeLink').value.toLowerCase();
    }
    this.telechargementService.postFile(this.fileToUpload, arrayParameters);
  }

  checkLinks(type:string) {
    if (type === 'url') {
      this.uploadForm.get('relativeLink').setValue('');
    } else if (type === 'relative') {
      this.uploadForm.get('url').setValue('');
    }
  }

  
  deleteTelechargement(id){
    return new Promise(
      (resolve,reject)=>{
          this.httpClient.post(this.httpRequest.TelechargementFile,{
              "login":sessionStorage.getItem("loginCompte"),
              "type":"delete",
              "id":id
          }).subscribe(data=>{
              resolve(data);
              this.snackBar.open("Le fichier est supprimé","x",{
                duration:3000
              });
          })
      }
  )
  }


}
