import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RepresentantComponent } from './representant.component';

describe('RepresentantComponent', () => {
  let component: RepresentantComponent;
  let fixture: ComponentFixture<RepresentantComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RepresentantComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RepresentantComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
