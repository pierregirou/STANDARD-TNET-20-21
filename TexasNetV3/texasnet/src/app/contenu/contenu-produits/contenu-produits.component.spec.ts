import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuProduitsComponent } from './contenu-produits.component';

describe('ContenuProduitsComponent', () => {
  let component: ContenuProduitsComponent;
  let fixture: ComponentFixture<ContenuProduitsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuProduitsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuProduitsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
